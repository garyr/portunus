<?php

namespace Portunus\Model;
use Portunus\Crypt\RSA\KeyPair;
use Portunus\Crypt\RSA\PublicKey;
use Portunus\Crypt\RSA\PrivateKey;

/**
 * Class Secret
 * @package Portunus\Model
 * @Entity @Table(name="secrets", uniqueConstraints={@UniqueConstraint(name="safe_key_idx", columns={"safe_id", "key"})})
 */
class Secret
{
    /**
     * @var integer
     *
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;

    /**
     * @var Safe
     *
     * @ManyToOne(targetEntity="Safe", inversedBy="secrets", cascade={"persist"})
     * @JoinColumn(name="safe_id", referencedColumnName="id")
     **/
    protected $safe;

    /**
     * @var string
     *
     * @Column(type="string", nullable=false)
     **/
    protected $key;

    /**
     * @var string
     *
     * @Column(type="string")
     **/
    protected $value;

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

    public function getId()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getValue(PrivateKey $privateKey = null)
    {
        $value = $this->value;
        if (!$privateKey) {
            return $value;
        }

        $chunkSize = $privateKey->getKeySize() / 8;
        $chunkCount = intval(ceil(strlen($value) / $chunkSize));

        $plainText = '';
        for ($i = 0; $i < $chunkCount; $i++) {
            $chunkData = substr($value, ($i * $chunkSize), $chunkSize);
            $plainText .= $privateKey->decrypt($chunkData);
        }

        if (empty($plainText)) {
            throw new \Exception(sprintf("Error decrypting text - OpenSSL Error string '%s'", openssl_error_string()));
        }

        return $plainText;
    }

    public function setValue(PublicKey $publicKey, $value)
    {
        $chunkSize = ($publicKey->getKeySize() / 8) - KeyPair::RSA_HEADER_LENGTH;
        $chunkCount = intval(ceil(strlen($value) / $chunkSize));

        $cipherText = '';
        for ($i = 0; $i < $chunkCount; $i++) {
            $chunkData = substr($value, ($i * $chunkSize), $chunkSize);
            $cipherText .= $publicKey->encrypt($chunkData);
        }

        if (empty($cipherText)) {
            throw new \Exception("Error encrypting plain text");
        }

        $this->value = $cipherText;
    }

    public function getSafe()
    {
        return $this->safe;
    }

    public function setSafe($safe)
    {
        $this->safe = $safe;
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
