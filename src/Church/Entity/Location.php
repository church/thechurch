<?php

namespace Church\Entity;

use Church\Entity\Place\Place;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Church\Entity\Location
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="location")
 */
class Location extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(name="location_id", type="string", length=255)
     * @ORM\Id
     * @Groups({"me_read", "me_write"})
     */
    private $id;

    /**
     * @var Place
     *
     * @ORM\ManyToOne(targetEntity="Church\Entity\Place\Place", inversedBy="locations")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="place_id")
     * @Groups({"me_read", "neighbor_read"})
     */
    private $place;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
     */
    private $longitude;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * Create new Location.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $id = $data['id'] ?? null;
        $this->id = is_string($id) ? $id : '';

        $place = $data['place'] ?? null;
        $this->place = $this->getSingle($place, Place::class);

        $latitude = $data['latitude'] ?? null;
        $this->latitude = is_numeric($latitude) ? (float) $latitude : null;

        $longitude = $data['longitude'] ?? null;
        $this->longitude = is_numeric($longitude) ? (float) $longitude : null;

        $created = $data['created'] ?? null;
        $this->created = $created instanceof \DateTimeInterface ? $created : null;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId(string $id) : self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set place
     *
     * @param Place $place
     */
    public function setPlace(Place $place) : self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return Place
     */
    public function getPlace() :? Place
    {
        return $this->place;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     */
    public function setLatitude(float $latitude) : self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     */
    public function getLatitude() :? float
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     */
    public function setLongitude(float $longitude) : self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     */
    public function getLongitude() :? float
    {
        return $this->longitude;
    }

    /**
     * Set created
     *
     * @param \DateTimeInterface $created
     */
    public function setCreated(\DateTimeInterface $created) : self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     */
    public function getCreated() :? \DateTimeInterface
    {
        return $this->created;
    }
}
