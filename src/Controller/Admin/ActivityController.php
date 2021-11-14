<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Datto\JsonRpc\Http\Client;
use Datto\JsonRpc\Http\Exceptions\HttpException;
use Datto\JsonRpc\Responses\ErrorResponse;
use ErrorException;

class ActivityController extends AbstractController
{
    const limit = 2;
    
    public function index(int $id = null): Response {
        
        $client = new Client('http://127.0.0.1:8000/json-rpc');
        $client->query('PostVisit', ['data' => date('Y-m-d'), 'url' => 'http://ozon.ru'], $result);
        try {
            $client->send();
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
        $client->query('GetVisits', is_null($id) ? [] : ['page' => $id, 'limit' => self::limit], $result);
        
        try {
            $client->send();
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
        
        $rows = '';
        foreach($result['rows'] as $row) {
            $rows .= "<tr><td>$row[url]</td><td>$row[count]</td><td>$row[last]</td></tr>";
        }
        
        $buts = '';
        for($i = 0; $i < $result['count'] / self::limit; $i++) {
            if($id === $i) {
                $buts .= " | <b>$i</b> ";
            }
            else {
                $buts .= " | <a href='/admin/activity/$i'>$i</a> ";
            }
        }

        return new Response(
            '<html><body>' . $buts . '<table>
                <tr><th>url</td><th>count</td><th>last</th></tr>
                ' . $rows . '</table></body></html>'
        );
    }
}

