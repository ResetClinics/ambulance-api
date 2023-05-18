<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Device;
use App\Flusher;
use App\Repository\DeviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeviceController extends AbstractController
{


    private DeviceRepository $devices;
    private Flusher $flusher;

    public function __construct(DeviceRepository $devices, Flusher $flusher)
    {
        $this->devices = $devices;
        $this->flusher = $flusher;
    }

    #[Route('/api/devices/{id}', name: 'device_create_or_update', methods: ['POST'])]
    public function createOrUpdate(string $id): Response
    {

        $device = $this->devices->find($id);

        if (!$device) {
            $device = new Device($id, $this->getUser());
            $this->devices->save($device);
        }else{
            $device->setUser($this->getUser());
        }

        $this->flusher->flush();

        return $this->json([]);
    }


    #[Route('/api/devices/{id}', name: 'device_delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $device = $this->devices->find($id);
         if (!$device){
             return $this->json([]);
         }
        $this->devices->remove($device);

        $this->flusher->flush();

        return $this->json([]);
    }
}
