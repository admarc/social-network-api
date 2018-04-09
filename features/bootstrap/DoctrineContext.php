<?php

use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Context;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use App\Entity\User;
use App\Entity\Post;

class DoctrineContext implements Context
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Given users exist in application:
     */
    public function usersExistInApplication(TableNode $table): void
    {
        foreach ($table->getHash() as $user) {
            $this->createUser($user['name'], $user['surname'], $user['email']);
        }

        $this->entityManager->flush();
    }

    /**
     * @Given posts exist in application:
     */
    public function postsExistInApplication(TableNode $table)
    {
        foreach ($table->getHash() as $post) {
            $createdAt = new \DateTime();
            if (isset($post['createdAt'])) {
                $createdAt = new \DateTime($post['createdAt']);
            }
            $this->createPost($post['title'], $post['content'], $post['user'], $createdAt);
        }

        $this->entityManager->flush();
    }

    /**
     * @Given User :follower is following user :followee
     */
    public function userIsFollowingUser(String $follower, String $followee)
    {
        $follower = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $follower]);
        $followee = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $followee]);

        $followee->addFollower($follower);

        $this->entityManager->flush();
    }

    public function createUser(string $name, string $surname, string $email)
    {
        $user = new User($name, $surname, $email);

        $plainPassword = 'pass';

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);

        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
    }

    public function createPost(string $title, string $content, string $userName, \DateTime $createdAt)
    {
        $post = new Post($title, $content);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userName]);

        $post->setUser($user);
        $this->setCreatedAt($post, $createdAt);

        $this->entityManager->persist($user);
        $this->entityManager->persist($post);
    }

    private function setCreatedAt($object, \Datetime $value)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty('createdAt');
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $event)
    {
        $this->buildSchema();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(AfterScenarioScope $event)
    {
        $this->entityManager->clear();
    }

    protected function buildSchema()
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $tool = new SchemaTool($this->entityManager);
            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);
        }
    }
}
