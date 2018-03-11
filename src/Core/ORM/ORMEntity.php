<?php

namespace Core\ORM;

use DateTime;
use stdClass;

class ORMEntity
{
    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var ORMTable
     */
    protected $ORMTable;

    /**
     * @var array
     */
    protected $primaryKey = [];

    /**
     * ORMEntity constructor.
     * @param ORMTable $ORMTable
     * @throws ORMException
     */
    public function __construct(ORMTable $ORMTable)
    {
        $this->ORMTable = $ORMTable;
        if ($this->tableName !== $ORMTable->getTableName()) {
            throw new ORMException("La Table \"{$ORMTable->getTableName()}\" passée en argument ne correspond par à l'entité créée.");
        }
        $this->typesSQLDefinition();

        date_default_timezone_set('Europe/Paris');
    }

    /**
     * @param $name
     * @param $value
     * @throws ORMException
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (is_callable([$this, $method])) {
            $this->$method($value);
        } else {
            if (array_key_exists($name, $this->ORMTable->getColumns())) {
                throw new ORMException("Vous n'avez pas l'autorisation de modifier la propriété \"{$name}\".");
            } else {
                throw new ORMException("La propriété \"{$name}\" n'existe pas.");
            }
        }
    }

    /**
     *
     * @param string $name
     * @throws ORMException
     * @return mixed
     */
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);

        if (is_callable([$this, $method])) {
            return static::$method();
        } else {
            if (array_key_exists($name, $this->ORMTable->getColumns())) {
                throw new ORMException("Vous n'avez pas l'autorisation d'accéder à la propriété \"{$name}\".");
            } else {
                throw new ORMException("La propriété \"{$name}\" n'existe pas.");
            }
        }
    }

    use ORMConfigSQL;

    /**
     * Utilise les éléments récupérés dans un stdClass pour créer un objet avec des propriétés avec le bon typage
     * Différencie si l'élément est une foreign key, si c'est le cas le place dans "colonne + Id"
     *
     * @uses valuesType
     * Récupère le typage dans l'ORMTable et la valeur à modifier
     * Renvoie la valeur avec le bon typage
     *
     * @param stdClass $class
     * @throws ORMException
     */
    public function constructWithStdclass(stdClass $class): void
    {
        foreach ($class as $key => $value) {
            if ($this->ORMTable->getColumns()[$key]['options']['foreign']) {
                $property = $key . 'Id';
                $this->$property = $this->valuesType($this->ORMTable->getColumns()[$key]['columnType'], $value);
            } else {
                $this->$key = $this->valuesType($this->ORMTable->getColumns()[$key]['columnType'], $value);
            }

            if ($this->ORMTable->getColumns()[$key]['options']['primary']) {
                $this->primaryKey[] = $key;
            }
        }
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return ORMTable
     */
    public function getORMTable(): ORMTable
    {
        return $this->ORMTable;
    }

    /**
     * @return array
     */
    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    /**
     * @param string $type
     * @param string $value
     * @return DateTime|int|null|string
     * @throws ORMException
     */
    private function valuesType(string $type, string $value)
    {
        if (in_array($type, $this->sqlString)) {
            return htmlspecialchars($value);
        } elseif (in_array($type, $this->sqlNumeric)) {
            return (int)$value;
        } elseif (in_array($type, $this->sqlDate)) {
            return new DateTime($value);
        } else {
            throw new ORMException("Le typage \"{$type}\" n'existe pas.");
        }
    }
}
