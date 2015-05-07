<?php

namespace Portunus\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Portunus\Crypt\Key;
use Portunus\Crypt\RSA\KeyPair;
use Portunus\Crypt\RSA\PublicKey;

/**
 * Class Safe
 * @package Portunus\Model
 * @Entity @Table(name="safes")
 */
class Safe
{
    /**
     * @var integer
     *
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     **/
    protected $id;

    /**
     * @var string
     *
     * @Column(type="string", nullable=false)
     **/
    protected $name;

    /**
     * @var Collection
     *
     * @OneToMany(targetEntity="Secret", mappedBy="safe", cascade={"remove"})
     */
    protected $secrets;

    /**
     * @var string
     *
     * @Column(type="string")
     **/
    protected $public_key;

    /**
     * @var \DateTime
     *
     * @Column(type="datetime")
     **/
    protected $created;

    /**
     * @var \DateTime
     *
     * @Column(type="datetime")
     **/
    protected $updated;

    public function __construct($name = null, $publicKey = null)
    {
        $this->setName($name);
        $this->setPublicKey($publicKey);
        $this->secrets = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Collection
     */
    public function getSecrets()
    {
        return $this->secrets;
    }

    /**
     * @return PublicKey
     */
    public function getPublicKey()
    {
        return new PublicKey($this->public_key);
    }

    public function setPublicKey(PublicKey $publicKey = null)
    {
        if ($publicKey) {
            $this->public_key = $publicKey->getKey();
        }
    }

    /**
     * @return Key
     */
    public function generateEncryptionKey()
    {
        $key = new Key();
        $key->setKey($key->generate());

        return $key;
    }

    /**
     * @param int $bits
     * @return KeyPair
     */
    public function generateKeyPair($bits = 1024)
    {
        $rsa = new KeyPair();
        $rsa->generate($bits);

        return $rsa;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated()
    {
        $this->created = new \DateTime();
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated()
    {
        $this->updated = new \DateTime();
    }
}
