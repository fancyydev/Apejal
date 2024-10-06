<?php

session_start();

require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/Apejal/Backend/login/log.php");
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/Apejal/Backend/DataBase/connectividad.php");

class Login {
    private $db;
    private $conexion;

    public function __construct() {
        header('Content-Type: application/json'); // Asegúrate de que la respuesta sea JSON
        try {
            $this->db = new DB_Connect();
            $this->conexion = $this->db->connect();

            $this->API();
        } catch (PDOException $e) {
            echo json_encode(['error' => '¡Error!: ' . $e->getMessage()]);
            die();
        }
    }

    public function API() {
        try {
            if (isset($_POST['Metodo'])) {
                switch ($_POST['Metodo']) {
                    case 'login':
                        $resultado = $this->login($_POST['Username'], $_POST['Password']);
                        if ($resultado && $resultado['estado']) {
                            $_SESSION["id"] = $resultado['id_usuario'];
                            $_SESSION["Zona"] = $_POST["Zona"];
                            $_SESSION["tipo"] = $resultado['id_tipo'];  // Almacena el tipo de usuario en la sesión

                            switch ($resultado['id_tipo']) {
                                case 1:
                                    echo json_encode(['estado' => true, 'tipo' => 'tipo1']);
                                    break;
                                case 2:
                                    echo json_encode(['estado' => true, 'tipo' => 'tipo2']);
                                    break;
                                case 3:
                                    echo json_encode(['estado' => true, 'tipo' => 'tipo3']);
                                    break;
                                case 4:
                                    echo json_encode(['estado' => true, 'tipo' => 'tipo4']);
                                    break;
                                case 5:
                                    echo json_encode(['estado' => true, 'tipo' => 'tipo5']);
                                    break;
                                default:
                                    echo json_encode(['estado' => false, 'error' => 'Tipo de usuario desconocido']);
                                    break;
                            }
                        } else {
                            echo json_encode(['estado' => false, 'error' => 'Credenciales incorrectas']);
                        }
                        break;
                    default:
                        echo json_encode(['error' => 'Método no válido']);
                        break;
                }
            } else {
                echo json_encode(['error' => 'No se proporcionó ningún método']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function login($username, $password) {
        try {
            // Consulta para verificar el usuario y obtener su tipo
            $sql = "SELECT IF(COUNT(id_usuario) > 0, true, false) AS estado, u.id_usuario, u.id_tipo
                    FROM usuario AS u
                    WHERE u.correo = :username AND u.contraseña = :password";
            
            $query = $this->conexion->prepare($sql);
            $query->bindParam(':username', $username);
            $query->bindParam(':password', $password);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            return $result;
        } catch (PDOException $e) {
            // Manejo de errores específicos de la base de datos si es necesario
            return ['estado' => false, 'error' => 'Error en la consulta a la base de datos'];
        }
    }
}



new Login();
