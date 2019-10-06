<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 * @ApiResource(
 *   subresourceOperations={
 *      "api_customers_invoices_get_subresource"={
 *          "normalization_context"={"groups" = {"invoices_subresource"}}
 *     }
 *     },
 *   itemOperations={"GET","PUT","DELETE","increment"={
 *     "method"= "post",
 *     "path"="/invoices/{id}/increment",
 *     "controller"="App\Controller\InvoiceIncrementationController",
 *     "swagger_context"={
 *          "summary"= "Incremente une facture",
 *          "description"="increment le chrono d'une facture donnée"
 *      }
 *     }
 *    },
 *   attributes={
 *    "pagination_enabled"=true,
 *    "pagination_items_per_page"= 20,
 *    "order": {"amount":"desc"}
 *   },
 *   normalizationContext={
 *     "groups"={"invoices_read"}
 *   },
 *   denormalizationContext={"disable_type_enforcement"=true}
 * )
 * @ApiFilter(OrderFilter::class, properties={"amout","sentAt"})
 *
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="le montant de la facture est obligatoire")
     * @Assert\Type(type="numeric", message="le montant doit etre numérique")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read", "customers_read","invoices_subresource"})
     * @Assert\DateTime(message="le format est YYYY-MM-DD")
     * @Assert\NotBlank(message="la date doit etre renseignée")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read", "customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="le statut de la facture est obligatoire")
     * @Assert\Choice(choices={"SENT","PAID","CANCELLED"}, message="le statut doit etre sent paid ou cancelled")
     *
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"invoices_read"})
     * @Assert\NotBlank(message="le client doit etre renseignée")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="Il faut un chrono pour la facture")
     * @Assert\Type(type="integer", message="Le chrono doit doit etre un nombre")
     */
    private $chrono;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Permet de recuperer le User gestionnaire de la facture
     * @Groups({"invoices_read","invoices_subresource"})
     * @return User
     */
    public function getUser(): User{
        return  $this->customer->getUser();
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount( $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt($sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono($chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
