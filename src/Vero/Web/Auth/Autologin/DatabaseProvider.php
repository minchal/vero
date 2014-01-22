<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Auth\Autologin;

use Doctrine\DBAL\Connection;
use Vero\Web\Auth\AutologinProvider;

/**
 * Autologin data in MySQL database with connection trought Doctrine\DBAL.
 * 
 * SQL to create table structure:
 * 
 * CREATE TABLE IF NOT EXISTS `autologin` (
 *  `id` varchar(40) NOT NULL,
 *  `time` int(11) NOT NULL,
 *  `user` text NOT NULL,
 *  PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * 
 */
class DatabaseProvider implements AutologinProvider
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
    public function __construct(Connection $db, $table = 'autologin')
    {
        $this -> db    = $db;
        $this -> table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function find($key, $ttl)
    {
        $data = $this -> db -> fetchColumn(
            'SELECT user FROM '.$this->table.' WHERE id = ? AND time > ?',
            [$key, time()-$ttl],
            0
        );
        
        if (!$data) {
            return false;
        }
        
        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function add($key, $userId, $ttl)
    {
        $this -> db -> executeUpdate(
            'INSERT INTO '.$this->table.' (id, time, user) VALUES (:id, :time, :user)',
            [
                'id' => $key,
                'user' => $userId,
                'time' => time()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return (boolean) $this -> db -> executeUpdate('DELETE FROM '.$this->table.' WHERE id = ?', array($key));
    }
}
