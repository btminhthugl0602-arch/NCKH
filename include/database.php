<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// ==================== INSERT ====================

/**
 * Insert dữ liệu vào bảng
 * @param string $table Tên bảng
 * @param array $data Mảng dữ liệu ['field' => 'value']
 * @return int|false ID của record vừa insert hoặc false nếu lỗi
 */
function db_insert($table, $data) {
    global $conn;
    
    try {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            return $conn->lastInsertId();
        }
        return false;
        
    } catch (PDOException $e) {
        if (_DEBUG) {
            error_log("DB Insert Error: " . $e->getMessage());
        }
        return false;
    }
}

// ==================== UPDATE ====================

/**
 * Update dữ liệu trong bảng
 * @param string $table Tên bảng
 * @param array $data Mảng dữ liệu cần update
 * @param string $where Điều kiện WHERE (ví dụ: "idTK = :id")
 * @param array $whereParams Tham số cho WHERE clause
 * @return bool
 */
function db_update($table, $data, $where, $whereParams = []) {
    global $conn;
    
    try {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :data_$key";
        }
        $setString = implode(', ', $set);
        
        $sql = "UPDATE $table SET $setString WHERE $where";
        $stmt = $conn->prepare($sql);
        
        // Bind data values
        foreach ($data as $key => $value) {
            $stmt->bindValue(":data_$key", $value);
        }
        
        // Bind where parameters
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
        
    } catch (PDOException $e) {
        if (_DEBUG) {
            error_log("DB Update Error: " . $e->getMessage());
        }
        return false;
    }
}

// ==================== DELETE ====================

/**
 * Xóa dữ liệu khỏi bảng
 * @param string $table Tên bảng
 * @param string $where Điều kiện WHERE
 * @param array $whereParams Tham số cho WHERE clause
 * @return bool
 */
function db_delete($table, $where, $whereParams = []) {
    global $conn;
    
    try {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $conn->prepare($sql);
        
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
        
    } catch (PDOException $e) {
        if (_DEBUG) {
            error_log("DB Delete Error: " . $e->getMessage());
        }
        return false;
    }
}

// ==================== SELECT ONE ====================

/**
 * Lấy 1 record từ bảng
 * @param string $table Tên bảng
 * @param string $where Điều kiện WHERE
 * @param array $whereParams Tham số cho WHERE
 * @return array|false
 */
function db_get_one($table, $where = '1=1', $whereParams = []) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM $table WHERE $where LIMIT 1";
        $stmt = $conn->prepare($sql);
        
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        if (_DEBUG) {
            error_log("DB Get One Error: " . $e->getMessage());
        }
        return false;
    }
}

// ==================== SELECT ALL ====================

/**
 * Lấy nhiều records từ bảng
 * @param string $table Tên bảng
 * @param string $where Điều kiện WHERE
 * @param array $whereParams Tham số
 * @param string $orderBy Sắp xếp (ví dụ: "ngayTao DESC")
 * @return array|false
 */
function db_get_all($table, $where = '1=1', $whereParams = [], $orderBy = '') {
    global $conn;
    
    try {
        $sql = "SELECT * FROM $table WHERE $where";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        $stmt = $conn->prepare($sql);
        
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        if (_DEBUG) {
            error_log("DB Get All Error: " . $e->getMessage());
        }
        return false;
    }
}

// ==================== QUERY ====================

/**
 * Thực thi query tùy chỉnh
 * @param string $sql Câu SQL query
 * @param array $params Tham số
 * @return array|false
 */
function db_query($sql, $params = []) {
    global $conn;
    
    try {
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        if (_DEBUG) {
            error_log("DB Query Error: " . $e->getMessage());
        }
        return false;
    }
}

// ==================== COUNT ====================

/**
 * Đếm số lượng records
 * @param string $table Tên bảng
 * @param string $where Điều kiện WHERE
 * @param array $whereParams Tham số
 * @return int
 */
function db_count($table, $where = '1=1', $whereParams = []) {
    global $conn;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM $table WHERE $where";
        $stmt = $conn->prepare($sql);
        
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
        
    } catch (PDOException $e) {
        if (_DEBUG) {
            error_log("DB Count Error: " . $e->getMessage());
        }
        return 0;
    }
}
