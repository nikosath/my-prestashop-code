<?php
if (!defined('_PS_VERSION_'))
  exit;

class SimpleMessage extends Module
{

  public function __construct()
  {
    $this->name                   = 'simplemessage';
    $this->tab                    = 'others';
    $this->version                = '1.0.0';
    $this->author                 = 'Nikos Athanasakis';
    $this->need_instance          = 0;
    $this->ps_versions_compliancy = array(
      'min' => '1.6',
      'max' => _PS_VERSION_
    );
    $this->bootstrap              = true;

    parent::__construct();

    $this->displayName = $this->l('Simple Message');
    $this->description = $this->l('Displays a custom message in the right-hand '
                                . 'side column of the product page.');

    $this->confirmUninstall = $this->l('Are you sure you want to uninstall '
                                     . 'this masterpiece?');

    if (!Configuration::get('MYMODULE_MESSAGE'))
      $this->warning = $this->l('No message provided');
  }

  public function install()
  {
    $message = 'I AM THE MESSAGE!!';
    return parent::install()
      && $this->registerHook('displayRightColumnProduct')
      && Configuration::updateValue('MYMODULE_MESSAGE', $message);
  }

  public function uninstall()
  {
    return parent::uninstall()
      && Configuration::deleteByName('MYMODULE_MESSAGE');
  }

  public function hookDisplayRightColumnProduct($params)
  {
    return Configuration::get('MYMODULE_MESSAGE');
  }

  public function getContent()
  {
    $output = null;

    if (Tools::isSubmit('submit' . $this->name)) {
      $my_module_name = strval(Tools::getValue('MYMODULE_MESSAGE'));
      if (!$my_module_name
        || empty($my_module_name)
        || !Validate::isGenericName($my_module_name)) {
        $output .= $this->displayError($this->l('Invalid Configuration value'));
      }
      else {
        Configuration::updateValue('MYMODULE_MESSAGE', $my_module_name);
        $output .= $this->displayConfirmation($this->l('Settings updated'));
      }
    }
    return $output . $this->displayForm();
  }

  public function displayForm()
  {
    // Get default language
    $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

    // Init Fields form array
    $fields_form[0]['form'] = array(
      'legend' => array(
        'title' => $this->l('Settings')
      ),
      'input' => array(
        array(
          'type' => 'text',
          'label' => $this->l('Configuration value'),
          'name' => 'MYMODULE_MESSAGE',
          'size' => 20,
          'required' => true
        )
      ),
      'submit' => array(
        'title' => $this->l('Save'),
        'class' => 'btn btn-default pull-right'
      )
    );

    $helper = new HelperForm();

    // Module, token and currentIndex
    $helper->module          = $this;
    $helper->name_controller = $this->name;
    $helper->token           = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex    = AdminController::$currentIndex . '&configure='
                             . $this->name;

    // Language
    $helper->default_form_language    = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;

    // Title and toolbar
    $helper->title          = $this->displayName;
    $helper->show_toolbar   = true; // false -> remove toolbar
    $helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the
                                   // top of the screen.
    $helper->submit_action  = 'submit' . $this->name;
    $helper->toolbar_btn    = array(
      'save' => array(
        'desc' => $this->l('Save'),
        'href' => AdminController::$currentIndex . '&configure=' . $this->name
                . '&save' . $this->name . '&token='
                . Tools::getAdminTokenLite('AdminModules')
      ),
      'back' => array(
        'href' => AdminController::$currentIndex . '&token='
                . Tools::getAdminTokenLite('AdminModules'),
        'desc' => $this->l('Back to list')
      )
    );

    // Load current value
    $helper->fields_value['MYMODULE_MESSAGE'] = Configuration::get('MYMODULE_MESSAGE');

    return $helper->generateForm($fields_form);
  }

}
