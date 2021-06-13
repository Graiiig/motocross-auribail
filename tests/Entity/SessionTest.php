<?php

namespace App\Test\Entity;

use App\Entity\Session;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class SessionTest extends KernelTestCase
{
    use FixturesTrait;

    public function getEntity(): Session
    {
        return (new Session())
            ->setTitle('Titre')
            ->setDate(new \DateTime())
            ->setStatus(0);
    }

    public function assertHasErrors(Session $session, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($session);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidBlankTitleEntity()
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 1);
    }
}
