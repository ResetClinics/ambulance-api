<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AdministratorReport;
use App\Entity\MedTeam\MedTeam;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\UserRepository;
use App\Services\TelegramSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'team:test',
    description: 'Init user for exchange',
)]
class TestCommand extends Command
{

    public function __construct(
        private readonly MedTeamRepository $medTeams,
        private TelegramSender                $tgSender,
        private readonly UserRepository $users,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = $this->users->get(122);

        $medTeam = $this->medTeams->find(5099);

        //$message = $this->buildReportMessage($medTeam, null, true);
//
        //dump($message);

        $message = "
        ОТЧЕТ 26.11.24 г.Москва\n
НОМЕР СМЕНЫ 5099\n
АДМИН: МустафинР.\n
ВРАЧ: СередоваВ.\n
ТИП СМЕНЫ суточная Сумма 4000\n
ПЕРЕРАБОТКА 170\n
\n
ВЫЕЗДЫ: 7\n
1. МаматовФ.А. id-37370 5000 * 10% = 500 \n
2. КлиманцевИ.В. id-37375 5000 * 10% = 500 \n
3. КлиманцевИ.В. id-37390 0 * 15% = 0  П\n
4. ПанасенкаТ.В. id-37393 35000 * 10% = 3500 \n
5. Ш.М. id-37400 25000 * 10% = 2500 \n
6. ПобединскийВ.В. id-37448 25000 * 10% = 2500 \n
7. - id-37459\n
ИТОГО ВЫЗОВЫ: 95000\n
ЗП Админ Выезды 9500\n
ЗП Врач Выезды 9500\n
\n
КОМБО: 0\n
\n
СТАЦИОНАР: 0\n
\n
ТРАНСПОРТНЫЕ\n
Пробег 0\n
Платка 0\n
Парковка 0\n
ИТОГО ТРАНСПОРТ: 0\n
\n
ВСЕГО ВЫРУЧКА 95000\n
ВСЕГО ЗП ВРАЧ 13670\n
        ";

        try {
            $this->tgSender->send($user, $message);
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        $this->tgSender->send($user, $message);

        $io->success('1');
        //$message = $this->buildReportMessage($medTeam, null, false);

       // dump($message);
        try {
            $this->tgSender->send($user, $message);
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        $io->success('2');

        return Command::SUCCESS;
    }

    public function buildReportMessage(MedTeam $data, ?AdministratorReport $report, bool $isDoctor): string
    {
        $message = [];

        $message[] = "ОТЧЕТ " . $data->getStartedAt()?->format('d.m.y') .
            ($data->getCity() ? ' г.' . $data->getCity()->getName() : "") . "\n";

        $message[] = "НОМЕР СМЕНЫ " . $data->getId() . "\n";
        $message[] = "АДМИН: " . $this->convertFio($data->getAdmin()->getName()) . "\n";
        $message[] = "ВРАЧ: " . $this->convertFio($data->getDoctor()->getName()) . "\n";
        if ($data->getDriver()) {
            $message[] = "ВОДИТЕЛЬ: " . $this->convertFio($data->getDriver()->getName()) . "\n";
        }

        $overTimeHoursReward = $data->getOverTimeHours() * 170;

        $message[] = "ТИП СМЕНЫ " . $data->getTypeTitle() . " Сумма " . ($isDoctor ? $data->getDoctorPrice() : $data->getAdminPrice() ) . "\n";
        $message[] = "ПЕРЕРАБОТКА " . $overTimeHoursReward . "\n";
        $message[] = "\n";

        $message[] = "ВЫЕЗДЫ: " . count($data->getCallings()) . "\n";

        $callsAmount = 0;
        $callsReward = 0;
        $sewingIn = 0;
        $surchargeForPenalty = 0;

        if (count($data->getCallings()) > 0) {
            foreach ($data->getCallings() as $key => $calling) {
                $amount = 0;
                $reward = 0;
                $surchargeForPenaltyCall = 0;

                $percent = 15;

                if (!$calling->isPersonal() && $calling->getCountRepeat() === 0) {
                    $percent = 10;
                }

                foreach ($calling->getServices() as $service) {
                    if (
                        !$service->isStationary() &&
                        !$service->isHospital() &&
                        !$service->isCoding() &&
                        !$service->getService()->isRepeatDesign() &&
                        $service->getService()->getId() !== 22 &&
                        $service->getService()->getId() !== 23
                    ) {
                        $amount += $service->getPrice();
                    }

                    if ($service->isCoding() && $service->getService()->getId() !== 16) {
                        $amount += $service->getPrice() - $service->getService()->getCoastPrice();
                    }

                    if ($service->isCoding() && $service->getService()->getId() === 16) {
                        $sewingIn += 2500;
                    }

                    if (
                        $service->getService()->getId() === 22 ||
                        $service->getService()->getId() === 23
                    ){
                        if ($service->getPrice() > 500){
                            $surchargeForPenaltyCall = 500;
                        }

                    }
                }

                $reward += $amount * $percent / 100;

                if ($surchargeForPenaltyCall > 0){
                    $message[] = $key + 1 . ". " . $this->convertFio($calling->getFio()) . " id-" . $calling->getId() .
                        " доплата за медотвод - " . $surchargeForPenaltyCall . "\n";
                }else{
                    $message[] = $key + 1 . ". " . $this->convertFio($calling->getFio()) . " id-" . $calling->getId() .
                        " " . $amount . " * " . $percent . "% = " . $reward . " " .
                        ($calling->getCountRepeat() > 0 ? ' П' : '') .
                        ($calling->isPersonal() ? ' И' : '') . "\n";
                }

                $surchargeForPenalty += $surchargeForPenaltyCall;
                $callsReward += $reward;
                $callsAmount += $amount;
            }

            $message[] = "ИТОГО ВЫЗОВЫ: " . $callsAmount . "\n";
            $message[] = "ЗП Админ Выезды " . $callsReward + $surchargeForPenalty . "\n";
            $message[] = "ЗП Врач Выезды " . $callsReward + $sewingIn + $surchargeForPenalty. "\n";

        }

        $message[] = "\n";

        $comboCount = 0;
        $comboMessage = [];
        $comboAmount = 0;

        //************ КОМБО ************
        if ($isDoctor){
            foreach ($data->getCallings() as $calling) {
                if ($calling->isDoctorCombo()) {
                    $comboCount++;
                    $comboMessage[] = $calling->getFio();
                    $therapySum = $calling->getTherapySum();
                    $comboMessage[] = $calling->getCompletedAt()?->format('d.m.y') . " id-" . $calling->getId() . " " . $therapySum . "*2.5% = " . $therapySum * 2.5 / 100  . "\n";
                    $owner = $calling->getOwner();
                    while ($owner) {
                        $therapySum = $owner->getTherapySum();
                        $comboMessage[] = $owner->getCompletedAt()?->format('d.m.y') . " id-" . $owner->getId() . " " . $therapySum . "*2.5% = " . $therapySum * 2.5 / 100  . "\n";
                        $owner = $owner->getOwner();
                    }
                }
            }
        }else{
            foreach ($data->getCallings() as $calling) {
                if ($calling->isAdminCombo()) {
                    $comboCount++;
                    $comboMessage[] = $calling->getFio();
                    $therapySum = $calling->getTherapySum();
                    $comboMessage[] = $calling->getCompletedAt()?->format('d.m.y') . " id-" . $calling->getId() . " " . $therapySum . "*2.5% = " . $therapySum * 2.5 / 100  . "\n";

                    $owner = $calling->getOwner();
                    while ($owner) {
                        $therapySum = $owner->getTherapySum();
                        $comboMessage[] = $owner->getCompletedAt()?->format('d.m.y') . " id-" . $owner->getId() . " " . $therapySum . "*2.5% = " . $therapySum * 2.5 / 100  . "\n";
                        $owner = $owner->getOwner();
                    }
                }
            }
        }

        $message[] = "КОМБО: " . $comboCount . "\n";

        if ($comboCount > 0) {

            $message = array_merge($message, $comboMessage);

            $message[] = "ИТОГО КОМБО: " . $comboAmount . "\n";
        }
        $message[] = "\n";


        //************ Стационары ************

        $stationaryCount = 0;
        $stationaryMessage = [];
        $stationaryAmount = 0;
        $stationaryReward = 0;

        foreach ($data->getCallings() as $calling) {
            $amount = 0;
            $reward = 0;
            $percent = 5;
            foreach ($calling->getServices() as $service) {
                if ($service->isStationary()) {
                    $amount += $service->getPrice();
                    $reward += $amount * 5 / 100;
                    $stationaryCount++;
                    $stationaryMessage[] = $stationaryCount . ". " . $this->convertFio($calling->getFio()) . " id-" . $calling->getId()
                        . " " . $amount . " * " . $percent . "% = " . $reward . "\n";
                }
            }

            $stationaryAmount += $amount;
            $stationaryReward += $reward;
        }

        $message[] = "СТАЦИОНАР: " . $stationaryCount . "\n";

        if ($stationaryCount > 0) {

            $message = array_merge($message, $stationaryMessage);

            $message[] = "ИТОГО СТАЦ: " . $stationaryAmount . "\n";
            $message[] = "ЗП Админ Стац " . $stationaryReward . "\n";
            $message[] = "ЗП Врач Стац " . $stationaryReward . "\n";

        }
        $message[] = "\n";

        //************ Госпитализации ************

        $hospitalsCount = 0;
        $hospitalsMessages = [];
        $hospitalsAmount = 0;
        $hospitalsReward = 0;


        foreach ($data->getCallings() as $calling) {
            $amount = 0;
            $reward = 0;
            $percent = 20;

            foreach ($calling->getServices() as $service) {
                if ($service->isHospital()) {
                    $amount += $service->getPrice();
                    $reward += $amount * $percent / 100;
                    $hospitalsCount++;
                    $hospitalsMessages[] = $hospitalsCount . ". " . $this->convertFio($calling->getFio()) . " id-" . $calling->getId() .
                        " " . $amount . " * " . $percent . "% = " . $reward . "\n";
                }
            }
            $hospitalsAmount += $amount;
            $hospitalsReward += $reward;
        }

        if ($hospitalsCount > 0) {
            $message[] = "ГОСПИТАЛИЗАЦИЯ:\n";
            $message = array_merge($message, $hospitalsMessages);
            $message[] = "ИТОГО ГОСПИТ: " . $hospitalsAmount . "\n";
            $message[] = "ЗП Админ Госпит " . $hospitalsReward . "\n";
            $message[] = "ЗП Врач Госпит " . $hospitalsReward . "\n";
            $message[] = "\n";
        }

        //************ Госпитализации ************

        $mileage = $report?->getMileage() ? $report->getMileage() * 7.5 : 0;
        $toolRoad = $report?->getToolRoad() ?: 0;
        $parkingFees = $report?->getParkingFees() ?: 0;

        $transportAmount = $mileage + $toolRoad + $parkingFees;

        $message[] = "ТРАНСПОРТНЫЕ\n";
        $message[] = "Пробег " . $mileage . "\n";
        $message[] = "Платка " . $toolRoad . "\n";
        $message[] = "Парковка " . $parkingFees . "\n";
        $message[] = "ИТОГО ТРАНСПОРТ: " . $transportAmount . "\n";
        $message[] = "\n";

        //************ Итоги ************

        $message[] = "ВСЕГО ВЫРУЧКА " . $callsAmount + $stationaryAmount + $hospitalsAmount . "\n";
        if ($isDoctor) {
            $message[] = "ВСЕГО ЗП ВРАЧ " .
                $data->getDoctorPrice() + $callsReward + $hospitalsReward + $stationaryReward + $overTimeHoursReward + $sewingIn + $surchargeForPenalty + $comboAmount . "\n";
        }else{
            $message[] = "ВСЕГО ЗП АДМИН " .
                $data->getDoctorPrice() + $callsReward + $hospitalsReward + $stationaryReward + $overTimeHoursReward + $surchargeForPenalty + $transportAmount + $comboAmount. "\n";
        }

        $result = implode("", $message);

        $result = mb_convert_encoding($result, "UTF-8");

        return htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
    }


    function convertFio($fio)
    {
        if (empty($fio)){
            return '-';
        }

        $parts = explode(" ", $fio);
        $result = $parts[0];

        if (count($parts) > 1) {
            $str = mb_substr($parts[1], 0, 1);
            if (!empty($str)){
                $result .= " " .$str . ".";
            }
        }

        if (count($parts) > 2) {
            $str = mb_substr($parts[2], 0, 1);
            if (!empty($str)){
                $result .= $str . ".";
            }

        }
        return $result;
    }
}
