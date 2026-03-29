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
     * Шаг 1: Запрос кода сброса пароля.
     * POST /partner/forgot-password
     * Body: { "phone": "79991234567" }
     */
    #[Route('/partner/forgot-password', name: 'partner_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $phone = preg_replace('/\D/', '', $data['phone'] ?? '');

        $this->logger->warning('RESET: forgot-password called', ['phone' => $phone]);

        if (strlen($phone) !== 11) {
            $this->logger->warning('RESET: invalid phone format', ['phone' => $phone]);
            return $this->json(['error' => 'Неверный формат телефона'], Response::HTTP_BAD_REQUEST);
        }

        // Проверяем что пользователь с таким телефоном существует
        $user = $this->em->getRepository(PartnerUser::class)->findOneBy(['phone' => $phone]);
        if (!$user) {
            $this->logger->warning('RESET: user NOT found', ['phone' => $phone]);
            return $this->json(['success' => true]);
        }

        $this->logger->warning('RESET: user found', ['phone' => $phone, 'name' => $user->getName()]);

        // Ограничение: не чаще 1 раза в 60 секунд
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
            $this->logger->warning('RESET: rate limited', ['phone' => $phone]);
            return $this->json(
                ['error' => 'Подождите минуту перед повторной отправкой'],
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        // Звоним пользователю — sms.ru возвращает 4-значный код
        $clientIp = $request->getClientIp() ?? '';
        $code = $this->smsRu->callCode($phone, $clientIp);

        $this->logger->warning('RESET: callCode result', ['phone' => $phone, 'code' => $code]);

        if ($code === null) {
            return $this->json(['error' => 'Не удалось совершить звонок. Попробуйте позже.'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $resetCode = new PasswordResetCode($phone, $code, 10);
        $this->em->persist($resetCode);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    /**
     * Шаг 2: Сброс пароля по коду.
     * POST /partner/reset-password
     * Body: { "phone": "79991234567", "code": "1234", "password": "newpass" }
     */
    #[Route('/partner/reset-password', name: 'partner_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $phone = preg_replace('/\D/', '', $data['phone'] ?? '');
        $code = $data['code'] ?? '';
        $password = $data['password'] ?? '';

        if (strlen($phone) !== 11 || strlen($code) !== 4) {
            return $this->json(['error' => 'Неверные данные'], Response::HTTP_BAD_REQUEST);
        }

        if (strlen($password) < 4) {
            return $this->json(['error' => 'Пароль должен быть не менее 4 символов'], Response::HTTP_BAD_REQUEST);
        }

        // Находим последний неиспользованный код
        $resetCode = $this->em->createQueryBuilder()
            ->select('c')
            ->from(PasswordResetCode::class, 'c')
            ->where('c.phone = :phone')
            ->andWhere('c.code = :code')
            ->andWhere('c.used = false')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('phone', $phone)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$resetCode || !$resetCode->isValid()) {
            return $this->json(['error' => 'Неверный или просроченный код'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository(PartnerUser::class)->findOneBy(['phone' => $phone]);
        if (!$user) {
            return $this->json(['error' => 'Пользователь не найден'], Response::HTTP_NOT_FOUND);
        }

        // Хешируем и сохраняем новый пароль
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $resetCode->markUsed();

        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
