<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Session\Backend;

use Vero\Web\Session\Backend;
use Doctrine\DBAL\Connection;

/**
 * Sessions data in MySQL database with connection trought Doctrine\DBAL.
 * 
 * SQL to create table structure:
 * 
 * CREATE TABLE IF NOT EXISTS `session` (
 *  `sid` varchar(40) NOT NULL,
 *  `time` int(11) NOT NULL,
 *  `data` text NOT NULL,
 *  PRIMARY KEY (`sid`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * 
 */
class Database implements Backend
{
    /**
     * @var Connection
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $table;
    
    /**
     * Construct backend with DB connection.
     * 
     * @param Connection
     * @param string
     */
    public function __construct(Connection $db, $table = 'session')
    {
        $this -> db    = $db;
        $this -> table = $table;
    }
    
    /**
     * {@inheritdoc}
     */
    public function load($id, $ttl)
    {
        $data = $this -> db -> fetchColumn(
            'SELECT data FROM '.$this->table.' WHERE sid = ? AND time > ?',
            [$id, time()-$ttl],
            0
        );
        
        if (!$data) {
            return false;
        }
        
        return unserialize($data);
    }
    
    /**
     * This method has implementation only for MySQL Database.
     * 
     * {@inheritdoc}
     */
    public function save($id, array $data, $ttl)
    {
        $this -> db -> executeUpdate(
            'INSERT INTO '.$this->table.' (sid, time, data) VALUES (:sid, :time, :data) '.
            'ON DUPLICATE KEY UPDATE time = :time, data = :data',
            [
                'sid' => $id,
                'data' => serialize($data),
                'time' => time()
            ]
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return (boolean) $this -> db -> executeUpdate('DELETE FROM '.$this->table.' WHERE sid = ?', array($id));
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear($ttl)
    {
        $this -> db -> executeUpdate('DELETE FROM '.$this->table.' WHERE time < ?', array(time()-$ttl));
    }
}
