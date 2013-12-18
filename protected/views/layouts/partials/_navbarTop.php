<?php
$isGuest = Yii::app()->user->isGuest;
$isCustomer = Yii::app()->user->checkAccess('customer');
$isStaff = Yii::app()->user->checkAccess('staff');
$isSuperUser = Yii::app()->user->checkAccess('superUser')
?>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <?= CHtml::link(Yii::app()->params['brand'], array('/site/index'), array('class' => 'brand')); ?>
            <div class="nav-collapse collapse">
                <?php
                    $this->widget('bootstrap.widgets.TbMenu', array(
                        'htmlOptions' => array(
                            'class' => 'pull-right',
                        ),
                        'items' => array(
                            array('label' => Yii::app()->user->name, 'url' => array('/customer/view', 'id' => $isGuest ? '' : Yii::app()->user->model->relatedCustomerSafe->id), 'visible' => !$isGuest),
                            array('label' => 'Logout', 'url' => array('/site/logout'), 'visible' => !$isGuest),
                            array('label' => 'Login', 'url' => array('/site/login'), 'visible' => $isGuest),
                            array('label' => 'Registrieren', 'url' => array('/site/register'), 'visible' => $isGuest),
                        ),
                    ));

                    $this->widget('bootstrap.widgets.TbMenu', array(
                        'items' => array(
                            array('label' => 'Beratungstermin vereinbaren', 'visible' => $isCustomer && !$isStaff, 'url' => array('/trip/create')),
                            array('label' => 'Ihre Termine', 'visible' => $isCustomer && !$isStaff, 'url' => array('/appointment/admin')),
                            array('label' => 'Kontakt & Lageplan', 'visible' => !$isStaff, 'url' => array('/site/contact')),

                            array('label' => 'Neu', 'visible' => $isStaff, 'items' => array(
                                array('label' => 'Beratungstermin', 'url' => array('/trip/create')),
                                array('label' => 'Termin', 'url' => array('/appointment/create')),
                                array('label' => 'Kunde', 'url' => array('/customer/create')),
                            )),
                            array('label' => 'Direktverkauf', 'url' => array('/bill/create'), 'visible' => $isStaff),
                            array('label' => 'Kalender', 'url' => array('/appointment/calendar'), 'visible' => $isStaff),
                            array('label' => 'Auswertung', 'visible' => $isStaff, 'items' => array(
                                array('label' => 'Abschluss', 'visible' => $isStaff, 'url' => '/bill/closing'),
                                array('label' => 'Bewertungen', 'url' => '/survey/admin'),
                            )),
                            array('label' => 'Administration', 'visible' => $isStaff, 'items' => array(
                                array('label' => 'Fallnummern', 'url' => array('/caseNumber/admin')),
                                array('label' => 'Kunden', 'url' => array('/customer/admin')),
                                array('label' => 'Produkte', 'url' => array('/product/admin')),
                                array('label' => 'Rechnungen', 'url' => array('/bill/admin')),
                                array('label' => 'Reisen', 'url' => array('/trip/admin')),
                                array('label' => 'Termine', 'url' => array('/appointment/admin')),
                            )),
                            array('label' => 'Systemeinstellungen', 'visible' => $isSuperUser, 'items' => array(
                                array('label' => 'Allgemein', 'url' => array('/setting/admin')),
                                array('label' => 'Bewertungsfragen', 'url' => array('/question/admin')),
                                array('label' => 'Reisetypen', 'url' => array('/tripType/admin')),
                                array('label' => 'Krankheiten', 'url' => array('/disease/admin')),
                                array('label' => 'Länder', 'url' => array('/country/admin')),
                                array('label' => 'Preiskategorien', 'url' => array('/customerCategory/admin')),
                                array('label' => 'Reisetypen', 'url' => array('/tripType/admin')),
                                array('label' => 'Zeitfenster', 'url' => array('/dateTimeframe/admin'), 'active' => in_array($this->route, array('dateTimeframe/admin', 'weekdayTimeframe/admin'))),
                            )),
                        ),
                    ));
                ?>
            </div>
        </div>
    </div>
</div>
