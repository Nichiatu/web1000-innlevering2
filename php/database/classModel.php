<?php
    require_once('utils.php');
    class ClassModel extends Utils {
        private $db;

        public function __construct(PDO $db) {
            if ($db) {
                $this->db = $db;
                return true;
            }
            throw new Exception('Database connection required.');
        }

        public function updateClass ($cc, $cn) {
            $cc = $this->validateClassCode($cc);
            $cn = $this->validateClassName($cn);
            
            if ($cc && $cn) {
                $stmt = $this->db->prepare("UPDATE klasse SET klassenavn = ? WHERE klassekode = ?");
                $stmt = $stmt->execute(array($cn, $cc));

                $return = array(
                    'cc' => $cc,
                    'cn' => $cn
                );

                if ($stmt) {
                    $return['success'] = true;
                } else {
                    $return['error'] = $stmt;
                }

                return $return;

            } else {
                return "error, cc and cn not valid";
            }

        }

        public function deleteClass (array $cc) {
            $cleanVals = $this->validateClassCodes($cc);
            $qMarks = $this->generateQMarks($cleanVals);
            $stmtP = $this->db->prepare("DELETE FROM klasse WHERE klassekode IN (" . $qMarks . ")");
            $stmt = $stmtP->execute($cleanVals);
            $return = array(
                'cc' => $cleanVals,
                'qMarks' => $qMarks
            );

            if ($stmt) {
                $return['success'] = true;
            } else  {
                $return['error'] = $stmtP->errorCode();
            }

            return $return;
        }

        public function postClass ($cc, $cn) {
            if ($this->validateClassCode($cc) && $this->validateClassName($cn)) {
                $cc = $this->validateClassCode($cc);
                $cn = $this->validateClassName($cn);
                $stmtP = $this->db->prepare('INSERT INTO klasse (klassekode, klassenavn) VALUES (?, ?)');
                $stmt = $stmtP->execute(array($cc, $cn));
                $return = array(
                    'cc' => $cc,
                    'cn' => $cn
                );

                if ($stmt) {
                    $return['success'] = 'Klassen, ' . $cn .  ', ble registrert!';
                } else if ($stmtP->errorCode() == 23000) {
                    $return['error'] = 'Klassekode kan ikke defineres flere ganger.';
                }
                return $return;
            }
            return array(
                'error' => 'Ikke en lovlig klassekode, eller klassenavn'
            );
        }


        public function countClasses () {
            $stmt = $this->db->query('SELECT count(klassekode) as classes FROM klasse');
            $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $stmt[0]['classes'];
        }

        public function getClassCodes () {
            return $this->db->query('SELECT klassekode FROM klasse')->fetchAll(PDO::FETCH_NUM);
          }  

        public function getClasses () {
            $stmt = $this->db->query('SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }