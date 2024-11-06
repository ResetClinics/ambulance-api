<?php

namespace App\Services\MedTeam;

use App\Entity\AdministratorReport;
use App\Entity\MedTeam\MedTeam;

class MedTeamReportMessageBuilder
{
    public function build(MedTeam $data, ?AdministratorReport $report, int $medTeamPrice = 0): string
    {
        $message = [];

        $message[] = "ОТЧЕТ " . $data->getStartedAt()?->format('d.m.y') . "\n";
        $message[] = "НОМЕР_СМЕНЫ " . $data->getId() . "\n";
        $message[] = "АДМИН: " . $this->convertFio($data->getAdmin()->getName()) . "\n";
        $message[] = "ВРАЧ: " . $this->convertFio($data->getDoctor()->getName()) . "\n";
        if ($data->getDriver()) {
            $message[] = "ВОДИТЕЛЬ: " . $this->convertFio($data->getDriver()->getName()) . "\n";
        }

        //todo
        $message[] = "ТИП_СМЕНЫ " . $data->getTypeTitle() . " Сумма " . $medTeamPrice . "\n";
        $message[] = "ПЕРЕРАБОТКА " . $data->getOverTimeHours() * 170 . "\n";
        $message[] = "\n";

        $message[] = "ВЫЕЗДЫ: " . count($data->getCallings()) . "\n";

        $callsAmount = 0;
        $callsReward = 0;

        if (count($data->getCallings()) > 0) {
            foreach ($data->getCallings() as $key => $calling) {
                $amount = 0;
                $reward = 0;

                $percent = 15;

                if (!$calling->isPersonal() && $calling->getCountRepeat() === 0) {
                    $percent = 10;
                }

                foreach ($calling->getServices() as $service) {
                    if (
                        !$service->isStationary() &&
                        !$service->isHospital() &&
                        !$service->isCoding() &&
                        !$service->getService()->isRepeatDesign()
                    ) {
                        $amount += $service->getPrice();
                    }
                }

                $reward += $amount * $percent / 100;

                $message[] = $key + 1 . ". " . $this->convertFio($calling->getName()) . "/" . $calling->getId() .
                    " " . $amount . " - " . $percent . "%" .
                    ($calling->getCountRepeat() > 0 ? ' П' : '') .
                    ($calling->isPersonal() ? ' И' : '') . "{\n";

                $callsReward += $reward;
                $callsAmount += $amount;
            }

            $message[] = "ИТОГО_ВЫЗОВЫ: " . $callsAmount . "\n";
            $message[] = "ЗП_Админ_Выезды " . $callsReward . "\n";
            $message[] = "ЗП_Врач_Выезды " . $callsReward . "\n";
            $message[] = "\n";
        }

        //************ Стационары ************

        $stationaryCount = 0;
        $stationaryMessage = [];
        $stationaryAmount = 0;
        $stationaryReward = 0;

        foreach ($data->getCallings() as $key => $calling) {
            $amount = 0;
            $reward = 0;
            $percent = 5;
            foreach ($calling->getServices() as $service) {
                if ($service->isStationary()) {
                    $amount += $service->getPrice();
                    $reward += $amount * 5 / 100;
                    $stationaryCount++;
                    $stationaryMessage[] = $key + 1 . ". " . $this->convertFio($calling->getName()) . "/" . $calling->getId()
                        . " " . $amount . " - " . $percent . "%" . "\n";
                }
            }

            $stationaryAmount += $amount;
            $stationaryReward += $reward;
        }

        $message[] = "СТАЦИОНАР: " . $stationaryCount . "\n";

        if ($stationaryCount > 0) {

            $message = array_merge($message, $stationaryMessage);

            $message[] = "ИТОГО_СТАЦ: " . $stationaryAmount . "\n";
            $message[] = "ЗП_Админ_Стац " . $stationaryReward . "\n";
            $message[] = "ЗП_Врач_Стац " . $stationaryReward . "\n";
            $message[] = "\n";
        }

        //************ Госпитализации ************

        $hospitalsCount = 0;
        $hospitalsMessages = [];
        $hospitalsAmount = 0;
        $hospitalsReward = 0;

        foreach ($data->getCallings() as $key => $calling) {
            $amount = 0;
            $reward = 0;
            $percent = 20;
            foreach ($calling->getServices() as $service) {
                if ($service->isHospital()) {
                    $amount += $service->getPrice();
                    $reward += $amount * $percent / 100;
                    $hospitalsCount++;
                    $hospitalsMessages[] = $key + 1 . ". " . $this->convertFio($calling->getName()) . "/" . $calling->getId() .
                        " " . $amount . " - " . $percent . "%" . "\n";
                }
            }
            $hospitalsAmount += $amount;
            $hospitalsReward += $reward;
        }

        if ($hospitalsCount > 0) {
            $message[] = "ГОСПИТАЛИЗАЦИЯ:\n";
            $message = array_merge($message, $hospitalsMessages);
            $message[] = "ИТОГО_ГОСПИТ: " . $hospitalsAmount . "\n";
            $message[] = "ЗП_Админ_Госпит " . $hospitalsReward . "\n";
            $message[] = "ЗП_Врач_Госпит " . $hospitalsReward . "\n";
            $message[] = "\n";
        }

        //************ Госпитализации ************

        $mileage = $report?->getMileage() ? $report->getMileage()* 7.5 : 0;
        $toolRoad = $report?->getToolRoad() ?: 0;
        $parkingFees = $report?->getParkingFees() ?: 0;

        $transportAmount = $mileage + $toolRoad + $parkingFees;

        $message[] = "ТРАНСПОРТНЫЕ\n";
        $message[] = "Пробег " . $mileage . "\n";
        $message[] = "Платка " . $toolRoad . "\n";
        $message[] = "Парковка " . $parkingFees . "\n";
        $message[] = "ИТОГО_ТРАНСПОРТ: " . $transportAmount . "\n";
        $message[] = "\n";

        //************ Итоги ************

        $message[] = "ВСЕГО_ВЫРУЧКА " . $callsAmount + $stationaryAmount + $hospitalsAmount. "\n";
        //$message[] = "НАЛ_К_СДАЧЕ ------\n";
        $message[] = "\n";

        $message[] = "ПРИМЕЧАНИЕ:\n";
        $message[] = "Не_учтено_комбо\n";

        $result = implode("", $message);
        return mb_convert_encoding($result, "UTF-8");
    }

    function convertFio($fio)
    {
        $parts = explode(" ", $fio);
        $result = $parts[0];
        if (count($parts) > 1) {
            $result .= " " . substr($parts[1], 0, 1) . ".";
        }

        if (count($parts) > 2) {
            $result .= substr($parts[2], 0, 1) . ".";
        }
        return $result;
    }

}