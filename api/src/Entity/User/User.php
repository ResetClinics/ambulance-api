<?php

declare(strict_types=1);

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\User\MyAction;
use App\Entity\Device;
use App\Entity\Team\Team;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
)]
#[UniqueEntity(fields: ['phone'], message: 'Этот номер телефона уже используется.')]
#[Post(uriTemplate: '/users/my', controller: MyAction::class, input: UserDto::class, read: false)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'team:item:get', 'team:item:get:team_my', 'calling:detail:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 11, unique: true)]
    #[Groups(['user:read', 'user:write', 'team:item:get', 'calling:detail:read'])]
    #[Assert\NotBlank(message: "Телефон обязателен для заполнения.")]
    #[Assert\Regex(
        pattern: '/\d{10}/',
        message: 'Номер телефона должен состоять из 10 цифр.'
    )]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:write', 'team:item:get', 'calling:detail:read'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:write', 'team:item:get'])]
    private ?string $position = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write', 'team:item:get'])]
    private ?string $avatar = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[Assert\Length(min: 4)]
    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'administrator', targetEntity: Team::class)]
    private Collection $teams;

    #[ORM\Column(nullable: true)]
    private ?int $externalId;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Device::class)]
    private Collection $devices;


    public function __construct($externalId = null)
    {
        $this->teams = new ArrayCollection();
        $this->externalId = $externalId;
        $this->devices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->phone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        /** @var array<array-key, string> $roles */
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        $this->teams->add($team);
        $team->setAdministrator($this);

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        $this->teams->removeElement($team);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return int
     */
    public function getExternalId(): int
    {
        return $this->externalId;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setUser($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        $this->devices->removeElement($device);

        return $this;
    }

    public static function createFromPayload($username, array $payload): self
    {
        return (new self())
            ->setId($username)
            ->setRoles($payload['roles'])
            ->setPhone($payload['phone'])
            ;
    }
}
