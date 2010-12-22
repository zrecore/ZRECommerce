<?php

class Zre_Controller_Crud_Json_Action extends Zend_Controller_Action
{
    /**
     * The internal data set.
     * @var Data_Set_Abstract
     */
    protected $_dataset = null;

    protected $_presetCreateFields = null;
    protected $_presetUpdateFields = null;

    protected $_updateFields = null;
    protected $_stripSlashesFields = null;

    protected function jsonCreateAction() {
        try {
            $params = $this->getRequest()->getParams();

            if (!empty($this->_presetCreateFields)) {
                $params = array_merge($params, $this->_presetCreateFields);
            }
            
            $id = $this->_dataset->create($params);

            $reply = array(
                'result' => 'ok',
                'data' => $id
            );
        } catch (Exception $e) {
            
            $reply = array(
                'result' => 'error',
                'data' => (string) $e
            );
        }

        $this->_helper->json($reply);
    }

    protected function jsonReadAction() {
        try {
            $request = $this->getRequest();
            $key = $request->getParam('key');
            
            $record = $this->_dataset->read($key);
            if ($record->count() > 0) {
                $record = $record->current()->toArray();
            } else {
                $record = null;
            }
            $reply = array(
                'result' => 'ok',
                'data' => $record
            );
        } catch (Exception $e) {
            $reply = array(
                'result' => 'error',
                'data' => (string) $e
            );
        }

        $this->_helper->json($reply);
    }

    protected function jsonUpdateAction() {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $fields = array();

            if (!empty($this->_presetUpdateFields)) {
                $params = array_merge($params, $this->_presetUpdateFields);
            }

            $primary = $this->_dataset->info('primary');
            $primary = array_pop($primary);

            if (!empty($this->_updateFields)) {
                $fields = $this->_updateFields;
            } else {

                
                $cols = $this->_dataset->info('cols');

                if (isset($cols[$primary])) unset($cols[$primary]);

                $fields = $cols;
            }

            $data = array();
            $where = null;
            if (isset($params[$primary])) {
                $where = $params[$primary];
            } else {
                throw new Exception('The primary key was not set.');
            }


            foreach ($fields as $field) {
                if (isset($params[$field]) && $field != $primary) {
                    $data[$field] = $params[$field];
                }
            }

            if (empty($data)) throw new Exception('No data was submitted.');

            $id = $this->_dataset->update($data, $where);

            $reply = array(
                'result' => 'ok',
                'data' => $id
            );
            
        } catch (Exception $e) {
            $reply = array(
                'result' => 'error',
                'data' => (string) $e
            );
        }

        $this->_helper->json($reply);
    }

    protected function jsonDeleteAction() {
        try {
            $request = $this->getRequest();
            $key = $request->getParam('key');

            $primary = $this->_dataset->info('primary');
            $primary = array_pop($primary);

            $where = $primary . ' = ?';
            $affectedRows = $this->_dataset->delete($where, $key);

            $reply = array(
                'result' => 'ok',
                'data' => $affectedRows
            );
        } catch (Exception $e) {
            $reply = array(
                'result' => 'error',
                'data' => (string) $e
            );
        }

        $this->_helper->json($reply);
    }

    protected function jsonListAction() {
        $reply = null;
        try {

            $request = $this->getRequest();
            
            $dataset = $this->_dataset;
            $primary = $dataset->info('primary');
            $primary = array_pop($primary);

            $sort = $request->getParam('sort', $primary);
            $order = $request->getParam('order', 'ASC');
            $page = $request->getParam('pageIndex', 1);
            $rowCount = $request->getParam('rowCount', 5);

            $options = array(
                    'order' => $sort . ' ' . $order,
                    'limit' => array(
                            'page' => $page,
                            'rowCount' => $rowCount
                    )
            );

            $totalRecords = $dataset->listAll(null, array(
                    'from' => array(
                            'name' => array('a' => $dataset->getModel()->info('name')),
                            'cols' => array(new Zend_Db_Expr('COUNT(*)'))
                    )
            ), false)->current()->offsetGet('COUNT(*)');

            $records = $dataset->listAll(null, $options);
            if (empty($records)) $records = null;
            
            // ...Run strip slashes on requested fields, if any.
            if (!empty($records) && !empty($this->_stripSlashesFields)) {
                foreach($records as $i => $r) {

                    foreach($r as $key => $value) {
                        if (in_array($key, $this->_stripSlashesFields)) {
                            $records[$i][$key] = stripslashes($records[$i][$key]);
                        }
                    }
                }
            }
            
            $reply = array(
                    'result' => 'ok',
                    'totalRows' => $totalRecords,
                    'data' => $records
            );

        } catch (Exception $e) {
            $reply = array(
                'result' => 'error',
                'data' => (string) $e
            );
        }

        $this->_helper->json($reply);
    }
}