<?php


/**
* Online Source: http://ralphschindler.com/2012/03/09/php-constructor-best-practices-and-the-prototype-pattern
*
*/

class DbAdapter {

    public function fetchAllFromTable($table) {
        return $arrayOfData;
    }
}

class RowGateway {

    public function __construct(DbAdapter $dbAdapter, $tableName) {
        $this->dbAdapter = $dbAdapter;
        $this->tableName = $tableName;
    }

    public function initialize($data) {
        $this->data = $data;
    }

    public function save() {}
    public function delete() {}
    public function refresh() {}
}

class UserRepository {

    public function __construct(DbAdapter $dbAdapter, RowGateway $rowGatewayPrototype = null) {
        $this->dbAdapter = $dbAdapter;
        $this->rowGatewayPrototype = ($rowGatewayPrototype) ? '' : new RowGateway($this->dbAdapter, 'user');
    }

    public function getUsers() {
        $rows = array();
        foreach ($this->dbAdapter->fetchAllFromTable('user') as $rowData) {
            $rows[] = $row = clone $this->rowGatewayPrototype;
            $row->initialize($rowData);
        }
        return $rows;
    }
}

class ReadWriteRowGateway extends RowGateway {

    public function __construct(DbAdapter $readDbAdapter, DbAdapter $writeDbAdapter, $tableName) {
        $this->readDbAdapter = $readDbAdapter;
        parent::__construct($writeDbAdapter, $tableName);
    }

    public function refresh() {
        // utilize $this->readDbAdapter instead of $this->dbAdapter in RowGateway base implementation
    }
}

//usage
$userRepository = new UserRepository(
    $dbAdapter,
    new ReadWriteRowGateway($readDbAdapter, $writeDbAdapter, 'user')
);
$users = $userRepository->getUsers();
$user = $users[0]; // instance of ReadWriteRowGateway with a specific row of data from the db
