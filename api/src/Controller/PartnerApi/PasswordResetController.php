<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Entity\Partner\PartnerUser;
use App\Entity\PasswordResetCode;
use App\Services\SmsRuService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SmsRuService $smsRu,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Шаг 1: Запрос сброса пароля — инициирует callcheck.
     * Возвращает номер, на который пользователь должен позвонить.
     */
    #[Route('/partner/forgot-password', name: 'partner_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $phone = preg_replace('/\D/', '', $data['phone'] ?? '');

        $this->logger->warning('RESET: forgot-password called', ['phone' => $phone]);

        if (strlen($phone) !== 11) {
            return $this->json(['error' => 'Неверный формат телефона'], Response::HTTP_BAD_REQUEST);
        }

        // Проверяем что пользователь существует
        $user = $this->em->getRepository(PartnerUser::class)->findOneBy(['phone' => $phone]);
        if (!$user) {
            $this->logger->warning('RESET: user NOT found', ['phone' => $phone]);
            // Не раскрываем что юзера нет
            return $this->json(['error' => 'Не удалось инициировать звонок'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        // Rate limit: не чаще 1 раза в 60 секунд
        $recent = $this->em->createQueryBuilder()
            ->select('c')
            ->from(PasswordResetCode::class, 'c')
            ->where('c.phone = :phone')
            ->andWhere('c.createdAt > :since')
            ->setParameter('phone', $phone)
            ->setParameter('since', new \DateTimeImmutable('-60 seconds'))
            ->getQuery()
            ->getResult();

        if (!empty($recent)) {
            return $this->json(
                ['error' => 'Подождите минуту перед повторной отправкой'],
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        // Вызываем callcheck/add — получаем номер для звонка
        $result = $this->smsRu->callcheckAdd($phone);

        $this->logger->warning('RESET: callcheckAdd result', ['phone' => $phone, 'result' => $result]);

        if ($result === null) {
            return $this->json(
                ['error' => 'Не удалось инициировать звонок. Попробуйте позже.'],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        // Сохраняем check_id в БД
        $resetCode = new PasswordResetCode($phone, $result['check_id'], 5);
        $this->em->persist($resetCode);
        $this->em->flush();

        return $this->json([
            'success'          => true,
            'call_phone'       => $result['call_phone'],
            'call_phone_pretty' => $result['call_phone_pretty'],
            'check_id'         => $result['check_id'],
        ]);
    }

    /**
     * Шаг 2: Проверка статуса звонка (фронт поллит этот эндпоинт).
     */
    #[Route('/partner/check-call-status', name: 'partner_check_call_status', methods: ['POST'])]
    public function checkCallStatus(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $checkId = $data['check_id'] ?? '';

        $this->logger->warning('CHECK-CALL-STATUS: called', [
            'check_id' => $checkId,
            'raw_body' => $request->getContent(),
        ]);

        if (empty($checkId)) {
            $this->logger->warning('CHECK-CALL-STATUS: empty check_id');
            return $this->json(['error' => 'check_id обязателен'], Response::HTTP_BAD_REQUEST);
        }

        // Находим запись в БД
        $resetCode = $this->em->getRepository(PasswordResetCode::class)
            ->findOneBy(['checkId' => $checkId]);

        if (!$resetCode) {
            $this->logger->warning('CHECK-CALL-STATUS: record NOT found in DB', ['check_id' => $checkId]);
            return $this->json(['status' => 'expired']);
        }

        if (!$resetCode->isValid()) {
            $this->logger->warning('CHECK-CALL-STATUS: record invalid (used or expired)', [
                'check_id' => $checkId,
                'isUsed' => $resetCode->isUsed(),
                'isExpired' => $resetCode->isExpired(),
            ]);
            return $this->json(['status' => 'expired']);
        }

        // Если уже подтверждён — сразу возвращаем
        if ($resetCode->isConfirmed()) {
            $this->logger->warning('CHECK-CALL-STATUS: already confirmed');
            return $this->json(['status' => 'confirmed']);
        }

        // Проверяем через sms.ru API
        $status = $this->smsRu->callcheckStatus($checkId);

        $this->logger->warning('CHECK-CALL-STATUS: smsru result', ['status' => $status]);

        if ($status === 'confirmed') {
            $resetCode->markConfirmed();
            $this->em->flush();
            return $this->json(['status' => 'confirmed']);
        }

        return $this->json(['status' => 'pending']);
    }

    /**
     * Шаг 3: Сброс пароля (только после подтверждения звонка).
     */
    #[Route('/partner/reset-password', name: 'partner_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $checkId = $data['check_id'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($checkId)) {
            return $this->json(['error' => 'check_id обязателен'], Response::HTTP_BAD_REQUEST);
        }

        if (strlen($password) < 4) {
            return $this->json(['error' => 'Пароль должен быть не менее 4 символов'], Response::HTTP_BAD_REQUEST);
        }

        // Находим подтверждённый, неиспользованный check
        $resetCode = $this->em->getRepository(PasswordResetCode::class)
            ->findOneBy(['checkId' => $checkId]);

        if (!$resetCode || !$resetCode->isValid() || !$resetCode->isConfirmed()) {
            return $this->json(['error' => 'Звонок не подтверждён или код просрочен'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository(PartnerUser::class)
            ->findOneBy(['phone' => $resetCode->getPhone()]);

        if (!$user) {
            return $this->json(['error' => 'Пользователь не найден'], Response::HTTP_NOT_FOUND);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $resetCode->markUsed();
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
