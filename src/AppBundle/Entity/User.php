<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 * @UniqueEntity("email")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="f_name", type="string", length=110, nullable=false)
     */
    private $fName;

    /**
     * @var string
     *
     * @ORM\Column(name="l_name", type="string", length=110, nullable=false)
     */
    private $lName;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=110, nullable=false, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @var string 
     *
     * @ORM\Column(name="password", type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="abc", type="string", length=110, nullable=false)
     */
    private $abc;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=false)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="admin", type="integer", nullable=false)
     */
    private $admin;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="user")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="SystSetting", mappedBy="user")
     */
    private $systSettings;
    
    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="user")
     */
    private $stocks;
    
    /**
     * @ORM\OneToMany(targetEntity="Sale", mappedBy="user")
     */
    private $sales;
    
    /**
     * @ORM\OneToMany(targetEntity="Duplicate", mappedBy="user")
     */
    private $duplicates;
    
    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="user")
     */
    private $categories;
    
    /**
     * @ORM\OneToMany(targetEntity="Banking", mappedBy="user")
     */
    private $bankings;
    
    /**
     * @ORM\OneToMany(targetEntity="Permission", mappedBy="user")
     */
    private $permissions;
    
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->systSettings = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->duplicates = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->bankings = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->active = true;
        $this->admin = false;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));    
    }

    public function __toString()
    {
        return $this->fName;
    }


    /**
     * Set fName
     *
     * @param string $fName
     *
     * @return User
     */
    public function setFName($fName)
    {
        $this->fName = $fName;

        return $this;
    }

    /**
     * Get fName
     *
     * @return string
     */
    public function getFName()
    {
        return $this->fName;
    }

    /**
     * Set lName
     *
     * @param string $lName
     *
     * @return User
     */
    public function setLName($lName)
    {
        $this->lName = $lName;

        return $this;
    }

    /**
     * Get lName
     *
     * @return string
     */
    public function getLName()
    {
        return $this->lName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * Set abc
     *
     * @param string $abc
     *
     * @return User
     */
    public function setAbc($abc)
    {
        $this->abc = $abc;

        return $this;
    }

    /**
     * Get abc
     *
     * @return string
     */
    public function getAbc()
    {
        return $this->abc;
    }

    /**
     * Set active
     *
     * @param integer $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return User
     */
    public function addProduct(\AppBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Product $product
     */
    public function removeProduct(\AppBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add systSetting
     *
     * @param \AppBundle\Entity\SystSetting $systSetting
     *
     * @return User
     */
    public function addSystSetting(\AppBundle\Entity\SystSetting $systSetting)
    {
        $this->systSettings[] = $systSetting;

        return $this;
    }

    /**
     * Remove systSetting
     *
     * @param \AppBundle\Entity\SystSetting $systSetting
     */
    public function removeSystSetting(\AppBundle\Entity\SystSetting $systSetting)
    {
        $this->systSettings->removeElement($systSetting);
    }

    /**
     * Get systSettings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSystSettings()
    {
        return $this->systSettings;
    }

    /**
     * Add stock
     *
     * @param \AppBundle\Entity\Stock $stock
     *
     * @return User
     */
    public function addStock(\AppBundle\Entity\Stock $stock)
    {
        $this->stocks[] = $stock;

        return $this;
    }

    /**
     * Remove stock
     *
     * @param \AppBundle\Entity\Stock $stock
     */
    public function removeStock(\AppBundle\Entity\Stock $stock)
    {
        $this->stocks->removeElement($stock);
    }

    /**
     * Get stocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStocks()
    {
        return $this->stocks;
    }

    /**
     * Add sale
     *
     * @param \AppBundle\Entity\Sale $sale
     *
     * @return User
     */
    public function addSale(\AppBundle\Entity\Sale $sale)
    {
        $this->sales[] = $sale;

        return $this;
    }

    /**
     * Remove sale
     *
     * @param \AppBundle\Entity\Sale $sale
     */
    public function removeSale(\AppBundle\Entity\Sale $sale)
    {
        $this->sales->removeElement($sale);
    }

    /**
     * Get sales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Add duplicate
     *
     * @param \AppBundle\Entity\Duplicate $duplicate
     *
     * @return User
     */
    public function addDuplicate(\AppBundle\Entity\Duplicate $duplicate)
    {
        $this->duplicates[] = $duplicate;

        return $this;
    }

    /**
     * Remove duplicate
     *
     * @param \AppBundle\Entity\Duplicate $duplicate
     */
    public function removeDuplicate(\AppBundle\Entity\Duplicate $duplicate)
    {
        $this->duplicates->removeElement($duplicate);
    }

    /**
     * Get duplicates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDuplicates()
    {
        return $this->duplicates;
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return User
     */
    public function addCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add banking
     *
     * @param \AppBundle\Entity\Banking $banking
     *
     * @return User
     */
    public function addBanking(\AppBundle\Entity\Banking $banking)
    {
        $this->bankings[] = $banking;

        return $this;
    }

    /**
     * Remove banking
     *
     * @param \AppBundle\Entity\Banking $banking
     */
    public function removeBanking(\AppBundle\Entity\Banking $banking)
    {
        $this->bankings->removeElement($banking);
    }

    /**
     * Get bankings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBankings()
    {
        return $this->bankings;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->active
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->active
        ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->active;
    }

    /**
     * Add permission
     *
     * @param \AppBundle\Entity\Permission $permission
     *
     * @return User
     */
    public function addPermission(\AppBundle\Entity\Permission $permission)
    {
        $this->permissions[] = $permission;

        return $this;
    }

    /**
     * Remove permission
     *
     * @param \AppBundle\Entity\Permission $permission
     */
    public function removePermission(\AppBundle\Entity\Permission $permission)
    {
        $this->permissions->removeElement($permission);
    }

    /**
     * Get permissions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set admin
     *
     * @param integer $admin
     *
     * @return User
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return integer
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}
