<?php
    require_once('utils.php');
    class ClassModel extends Utils {
        
        public function getClassCodes () {
                return $this->db->query('SELECT klassekode FROM klasse')->fetchAll(PDO::FETCH_NUM);
            }

        public function postClass ($cc, $cn) {
            if ($this->validateClassCode($cc) && $this->validateClassName($cn)) {
                $cc = $this->validateClassCode($cc);
                $cn = $this->validateClassName($cn);
                $stmt = $this->db->prepare('INSERT INTO klasse (klassekode, klassenavn) VALUES (?, ?)');
                $stmt = $stmt->execute(array($cc, $cn));
                return array(
                    'cc' => $cc,
                    'cn' => $cn,
                    'success' => $stmt
                );
            }
            return array(
                'error' => 0
            );
        }

        public function countClasses () {
            $stmt = $this->db->query('SELECT count(klassekode) as classes FROM klasse');
            $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $stmt[0]['classes'];
        }
    }