<?php

namespace Infrastructure\Services;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    const CONSTRAINTS_PATH = 'Symfony\Component\Validator\Constraints';
    private ValidatorInterface $validator;
    private array $errors;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
        $this->errors = [];
    }

    public function name($property, string $key = null): void
    {
        $result = $this->validator->validate($property, [
            new NotBlank(),
            new Email()
        ]);

        $this->add(end($result), $key);
        die;
    }

    private function add(string $error, $key = null)
    {
        if ($key) {
            if (!$this->hasError($key)) {
                throw new \DomainException("Validator key $key already exists");
            }
            $this->errors[$key] = $error;
        }

        $this->errors[] = $error;
    }

    public function hasError(string $key): bool
    {
        return array_key_exists($key, $this->errors);
    }

    public function get(string $key): string
    {
        if (!$this->hasError($key)) {
            throw new \DomainException("Error $key does not exist.");
        }

        return $this->errors[$key];
    }

    public function count(): int
    {
        return count($this->errors);
    }

    public function validateDto(object $dto): void
    {
        $class = get_class($dto);
        $rc = new \ReflectionClass($class);
        $
        $propertiesReflection = $rc->getProperties();
        foreach ($propertiesReflection as $propertyReflection) {
            $docs = $propertyReflection->getDocComment();
            if (strlen($docs) > 40) {
                $res = preg_match_all("/@Assert\\\[A-Za-z]*/", $docs, $matches);
                if ($constraints = $matches[0]) {
                    if (!is_array($constraints)) {
                        $constraints = [$constraints];
                    }
                    foreach ($constraints as $constraint) {
                        $constraint = str_replace('@Assert', self::CONSTRAINTS_PATH, $constraint);
                        if (class_alias($constraint)) {
                           $constraint = new $constraint();
                        }
                    }
                }
            }
            echo 'nothing <br>';
        }
        die;
    }
}