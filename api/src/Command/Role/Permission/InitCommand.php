<?php

namespace App\Command\Role\Permission;

use App\Entity\Role\Permission;
use App\Flusher;
use App\Repository\Role\PermissionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'role:permission:init',
    description: 'Обновление разрешений системы доступа',
)]
class InitCommand extends Command
{

    private $data = [
        //вызовы
        [
            'id' => 'calls-index',
            'description' => 'Вызовы: просмотр списка'
        ],
        [
            'id' => 'calls-create',
            'description' => 'Вызовы: добавление'
        ],
        [
            'id' => 'calls-view',
            'description' => 'Вызовы: просмотр детально'
        ],
        [
            'id' => 'calls-edit',
            'description' => 'Вызовы: изменение'
        ],
        [
            'id' => 'calls-delete',
            'description' => 'Вызовы: удаление'
        ],
        //Партнеры
        [
            'id' => 'partners-index',
            'description' => 'Партнеры: просмотр списка'
        ],
        [
            'id' => 'partners-create',
            'description' => 'Партнеры: добавление'
        ],
        [
            'id' => 'partners-view',
            'description' => 'Партнеры: просмотр детально'
        ],
        [
            'id' => 'partners-edit',
            'description' => 'Партнеры: изменение'
        ],
        [
            'id' => 'partners-delete',
            'description' => 'Партнеры: удаление'
        ],
        //Пользователи
        [
            'id' => 'users-index',
            'description' => 'Пользователи: просмотр списка'
        ],
        [
            'id' => 'users-create',
            'description' => 'Пользователи: добавление'
        ],
        [
            'id' => 'users-view',
            'description' => 'Пользователи: просмотр детально'
        ],
        [
            'id' => 'users-edit',
            'description' => 'Пользователи: изменение'
        ],
        [
            'id' => 'users-delete',
            'description' => 'Пользователи: удаление'
        ],
        //Карта
        [
            'id' => 'maps-index',
            'description' => 'Карта: просмотр карты'
        ],
        //Бригады
        [
            'id' => 'med_teams-index',
            'description' => 'Бригады: просмотр списка'
        ],
        [
            'id' => 'med_teams-create',
            'description' => 'Бригады: добавление'
        ],
        [
            'id' => 'med_teams-view',
            'description' => 'Бригады: просмотр детально'
        ],
        [
            'id' => 'med_teams-edit',
            'description' => 'Бригады: изменение'
        ],
        [
            'id' => 'med_teams-delete',
            'description' => 'Бригады: удаление'
        ],
        //Стационар
        [
            'id' => 'hospitals-index',
            'description' => 'Стационар: просмотр списка'
        ],
        [
            'id' => 'hospitals-create',
            'description' => 'Стационар: добавление'
        ],
        [
            'id' => 'hospitals-view',
            'description' => 'Стационар: просмотр детально'
        ],
        [
            'id' => 'hospitals-edit',
            'description' => 'Стационар: изменение'
        ],
        [
            'id' => 'hospitals-delete',
            'description' => 'Стационар: удаление'
        ],
        //Роли
        [
            'id' => 'roles-index',
            'description' => 'Роли: просмотр списка'
        ],
        [
            'id' => 'roles-create',
            'description' => 'Роли: добавление'
        ],
        [
            'id' => 'roles-view',
            'description' => 'Роли: просмотр детально'
        ],
        [
            'id' => 'roles-edit',
            'description' => 'Роли: изменение'
        ],
        [
            'id' => 'roles-delete',
            'description' => 'Роли: удаление'
        ],
        //Разрешения
        [
            'id' => 'permissions-index',
            'description' => 'Разрешения: просмотр списка'
        ],
    ];

    public function __construct(
        private readonly PermissionRepository $permissions,
        private readonly Flusher $flusher,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->data as $dto){
            $permission = $this->permissions->find($dto['id']);
            if (!$permission){
                $permission = new Permission(
                    $dto['id'],
                    $dto['description']
                );
                $this->permissions->add($permission);
            }else{
                $permission->setDescription($dto['description']);
            }
        }
        $this->flusher->flush();

        $io->success('Разрешения в системе доступа обновлены.');

        return Command::SUCCESS;
    }
}
