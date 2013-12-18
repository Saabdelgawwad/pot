<?php

class AuthManager extends CPhpAuthManager
{
    public function init()
    {
        parent::init();
        $this->setup();
    }
    
    public function load()
    {
        // Zuordnungen werden nicht in File gecacht.
    }
    
    public function save()
    {
        // Zuordnungen werden nicht in File gecacht.
    }
    
    public function checkAccess($itemName, $userId, $params=array())
    {
        // Rollen-Zuordnungen aus der DB lesen
        if (isset($userId) and $this->getAuthAssignments($userId) === array()) {
            $user = User::model()->active()->findByPk($userId);
            if (isset($user)) {
                $this->assign($user->role, $user->id);
            }
        }
        
        $params['userId'] = $userId;
        
        return parent::checkAccess($itemName, $userId, $params);
    }

    /*
     * Um BizRules etwas zu kürzen, wurde das Verhalten von Yii etwas angepasst.
     * Eine BizRule ist neu eine anonyme PHP-Funktion, die entweder ein oder zwei
     * Parameter entgegennimmt (User-Model, bzw. ID des Entities und User-Model)
     * und ein Boolean zurückgibt.
     * 
     * Um die Zugriffsrechte für eine bestimmtes Entity zu prüfen, soll z.B.
     * folgendes aufgerufen werden:
     * Yii::app()->user->checkAccess('updateEntity', array('id' => 123));
     */
    public function executeBizRule($bizRule, $params, $data)
    {
        if ($bizRule === null) {
            return true;
        } else {
            $user = User::model()->findByPk($params['userId']);
            if (!isset($user)) {
                return false;
            }

            if (array_key_exists('id', $params)) {
                return $bizRule($params['id'], $user);
            } else {
                return $bizRule($user);
            }
        }
    }
    
    protected function setup()
    {
        // Die Zuordnungen werden bei jedem Seitenaufruf neu erzeugt und nicht
        // gecacht. Das sollte performancetechnisch kein Problem sein.
        
        $this->createOperation('appointment.view', '', function ($id, $user) {
            $appointment = Appointment::model()->findByPk($id);
            if (!isset($appointment))
                throw new CHttpException(404);
            return ($user->checkAccess('staff'));
                // Vorerst kann nur das Personal Termindetail öffnen
                //or ($user->checkAccess('customer') and $appointment->customer_id == $user->relatedCustomer->id)
                //or ($user->checkAccess('customer') and count($appointment->relatedConsultations(array('condition' => 'customer_id = :customer', 'params' => array(':customer' => $user->relatedCustomer->id)))));
        });
        
        $this->createOperation('appointment.setDate', '', function ($id, $user) {
            $appointment = Appointment::model()->findByPk($id);
            if (!isset($appointment))
                throw new CHttpException(404);
            if (isset($appointment->started))
                return false;
            return ($user->checkAccess('staff'))
                or ($user->checkAccess('customer') and $appointment->customer_id == $user->relatedCustomer->id and !isset($appointment->date) and (!isset($appointment->provisory_date) or strtotime('today') <= $appointment->provisory_date->getTimestamp()));
        });
        
        $this->createOperation('appointment.update', '', function ($id, $user) {
            $appointment = Appointment::model()->findByPk($id);
            if (!isset($appointment))
                throw new CHttpException(404);
            return !isset($appointment->started);
        });
        
        $this->createOperation('appointment.delete', '', function ($id, $user) {
            return $user->checkAccess('appointment.update', array('id' => $id));
        });
        
        $this->createOperation('appointment.startConsultations', '', function ($id, $user) {
            $appointment = Appointment::model()->findByPk($id);
            if (!isset($appointment))
                throw new CHttpException(404);
            return !isset($appointment->started) && isset($appointment->relatedCustomer);
        });
        
        $this->createOperation('appointment.endConsultations', '', function ($id, $user) {
            $appointment = Appointment::model()->findByPk($id);
            if (!isset($appointment))
                throw new CHttpException(404);
            return isset($appointment->started) && !isset($appointment->ended) && isset($appointment->relatedCustomer);
        });
        
        $this->createOperation('appointment.arrangeNextAppointment', '', function ($id, $user) {
            $appointment = Appointment::model()->findByPk($id);
            if (!isset($appointment))
                throw new CHttpException(404);
            return isset($appointment->ended) && isset($appointment->relatedCustomer);
        });
        
        $this->createOperation('trip.view', '', function ($id, $user) {
            $trip = Trip::model()->findByPk($id);
            if (!isset($trip))
                throw new CHttpException(404);
            return ($user->checkAccess('staff'))
                or ($user->checkAccess('customer') and $trip->customer_id == $user->relatedCustomer->id);
        });
        
        $this->createOperation('trip.delete', '', function ($id, $user) {
            $trip = Trip::model()->findByPk($id);
            if (!isset($trip))
                throw new CHttpException(404);
            return ($user->checkAccess('staff') and (!isset($trip->relatedInitialAppointment) or $user->checkAccess('appointment.delete', array('id' => $trip->relatedInitialAppointment->id))))
                or ($user->checkAccess('customer') and $trip->customer_id == $user->relatedCustomer->id and (!isset($trip->relatedInitialAppointment) or !isset($trip->relatedInitialAppointment->date)));
        });
        
        $this->createOperation('customer.view', '', function ($id, $user) {
            $customer = Customer::model()->findByPk($id);
            if (!isset($customer))
                throw new CHttpException(404);
            return ($user->checkAccess('staff'))
                or ($user->checkAccess('customer') and $customer->id == $user->relatedCustomer->id);
        });
        
        $this->createOperation('customer.update', '', function ($id, $user) {
            return $user->checkAccess('customer.view', array('id' => $id));
        });
        
        $this->createOperation('user.changePassword', '', function ($id, $user) {
            $userModel = User::model()->findByPk($id);
            if (!isset($userModel))
                throw new CHttpException(404);
            return ($user->checkAccess('staff'))
                or ($user->checkAccess('customer') and $userModel->id == $user->id);
        });
        
        $this->createOperation('consultation.view', '', function ($id, $user) {
            $consultation = Consultation::model()->findByPk($id);
            if (!isset($consultation))
                throw new CHttpException(404);
            return $user->checkAccess('appointment.view', array('id' => $consultation->relatedAppointment->id)) and isset($consultation->relatedAppointment->ended);
        });
        
        $this->createOperation('consultation.update', '', function ($id, $user) {
            $consultation = Consultation::model()->findByPk($id);
            if (!isset($consultation))
                throw new CHttpException(404);
            return !isset($consultation->relatedAppointment->ended);
        });
        
        $this->createOperation('bill.update', '', function ($id, $user) {
            $bill = Bill::model()->findByPk($id);
            if (!isset($bill))
                throw new CHttpException(404);
            return !$bill->isPaid();
        });
        
        
        // Customer
        $customer = $this->createRole('customer');
        $customer->addChild('appointment.view');
        $customer->addChild('appointment.setDate');
        $customer->addChild('trip.view');
        $customer->addChild('trip.delete');
        $customer->addChild('customer.view');
        $customer->addChild('customer.update');
        $customer->addChild('user.changePassword');
        $customer->addChild('consultation.view');
        
        // Staff
        $staff = $this->createRole('staff');
        $staff->addChild('customer'); // von Rolle "customer" erben
        $staff->addChild('appointment.update');
        $staff->addChild('appointment.delete');
        $staff->addChild('appointment.startConsultations');
        $staff->addChild('appointment.endConsultations');
        $staff->addChild('appointment.arrangeNextAppointment');
        $staff->addChild('consultation.update');
        $staff->addChild('bill.update');
        
        // SuperUser
        $superUser = $this->createRole('superUser');
        $superUser->addChild('staff'); // von Rolle "staff" erben
        
        // Admin
        $admin = $this->createRole('admin');
        $admin->addChild('superUser'); // von Rolle "superUser" erben
        
        // Default-Rollen
        $this->defaultRoles = array();
    }
}
