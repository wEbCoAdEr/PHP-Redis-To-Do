<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Handle extends CI_Controller
{

    private string $requestMethod;
    private string $todos;

    public function __construct()
    {
        parent::__construct();

        //Load Redis cache driver as default and file cache driver as backup
        $this->load->driver('cache', array('adapter' => 'redis', 'backup' => 'file'));

        //Check if Redis is supported in the current environment and response with required message and http response code
        if (!$this->cache->redis->is_supported()) {
            $this->outputData([
                'status' => 0,
                'message' => 'This application requires Redis. Please configure Redis from <code>application/config/redis.php</code>'
            ], 500);
        }

        //Check if any to-do list is available in redis storage and initiate empty data if not available
        if ($this->cache->get('todoData') == null) {
            $this->cache->save('todoData', json_encode([]), 604800);
        }

        //Assigns to-do data from redis storage to todos property
        $this->todos = $this->cache->get('todoData');

        //set request method to property
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];

    }

    public function index()
    {
        //call request handler method
        $this->handleRequest();
    }

    //Handle API request
    private function handleRequest()
    {

        //Calls to do list fetch method if the request method is GET
        if ($this->requestMethod === 'GET') {
            $this->fetchTodoList();
        }

        //Calls to-do list add method if request method is POST
        if ($this->requestMethod === 'POST') {
            $this->addTodoList();
        }

        //Calls to-do update method if request method is PUT
        if ($this->requestMethod === 'PUT') {
            $this->updateTodo();
        }

        //Calls to-do delete method if request method is DELETE
        if ($this->requestMethod === 'DELETE') {
            $this->deleteTodo();
        }

    }

    //Add to-do list data
    private function addTodoList()
    {
        //Get and convert current data to array
        $currentData = (array)json_decode($this->todos, true);

        //Calculate new data index
        $newDataIndex = count($currentData);

        //create new to-do data array
        $todoData = [
            'id' => $newDataIndex,
            'task' => $this->input->post('task'),
            'date' => $this->input->post('date'),
            'status' => ''
        ];

        //Update current to-do data with new to-do data
        $currentData[] = $todoData;

        //Store new to-do list in redis
        if ($this->cache->save('todoData', json_encode($currentData), 604800)) {
            //output request response
            $this->outputData([
                'status' => 1,
                'message' => 'New to-do item added',
                'id' => $newDataIndex
            ], 201);
        }
    }

    //Update to-do data
    private function updateTodo()
    {
        //Get and convert current data to array
        $currentData = (array)json_decode($this->todos, true);

        //Read raw data (json) from request body using php input stream
        $getPutData = (array)json_decode(file_get_contents("php://input"), true);

        //Get to-do index as id
        $index = isset($getPutData['id']) ? (int)$getPutData['id'] : '';
        $getStatus = isset($getPutData['status']) ? (int)$getPutData['status'] : '';

        //Update to-do data to current data array
        if (!empty($getPutData['task'])) {
            $currentData[$index]['task'] = $getPutData['task'];
        }

        if (!empty($getPutData['date'])) {
            $currentData[$index]['date'] = $getPutData['date'];
        }

        if (empty($getPutData['task']) && ($getStatus == 1 || $getStatus == 0)) {
            $currentData[$index]['status'] = $getStatus;
        }

        //Store updated to-do list in redis
        if ($this->cache->save('todoData', json_encode($currentData), 604800)) {
            //output request response
            $this->outputData([
                'status' => 1,
                'message' => 'To-do item updated',
            ]);

        }

    }

    //Delete to-do data
    private function deleteTodo()
    {
        //Get and convert current data to array
        $currentData = (array)json_decode($this->todos, true);

        //Read raw data (json) from request body using php input stream
        $getDeleteData = (array)json_decode(file_get_contents("php://input"), true);

        //Get to-do index as id
        $index = $getDeleteData['id'];

        //Delete to-do item from current data array
        unset($currentData[$index]);
        $currentData = array_values($currentData);

        //Update current to-do data id to match item index
        for ($i = $index; $i < count($currentData); $i++) {
            $currentData[$i]['id'] = $i;
        }

        //Store updated to-do list in redis
        if ($this->cache->save('todoData', json_encode($currentData), 604800)) {
            //output request response
            $this->outputData([
                'status' => 1,
                'message' => 'To-do item deleted',
                'updated_data' => $currentData
            ]);
        }

    }

    //Get to-do list data
    private function fetchTodoList()
    {
        //output request response
        $this->outputData([
            'status' => 1,
            'message' => 'to-do list data fetch successful',
            'data' => (array)json_decode($this->todos, true)
        ]);
    }

    //Output json response
    private function outputData(array $outputData, $httpStatusCode = 200)
    {
        //Set header content type
        header('Content-Type: application/json');

        //Set http response ode
        http_response_code($httpStatusCode);
        //encode output data array to json and output json data

        echo json_encode($outputData);
        //exit process after the completion of response output

        exit();
    }


}
