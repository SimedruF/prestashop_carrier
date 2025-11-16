<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Ahscarrierzip extends Module
{
    public function __construct()
    {
        $this->name = 'ahscarrierzip';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Automatic House Systems SRL';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Carrier ZIP filter');
        $this->description = $this->l('Filter carriers by customer postal code (prefix-based rules).');

        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('filterCarrierList');
    }

    public function uninstall()
    {
        // curăță toate config-urile proprii
        $carriers = Carrier::getCarriers(
            (int)Configuration::get('PS_LANG_DEFAULT'),
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );

        foreach ($carriers as $carrier) {
            Configuration::deleteByName($this->getConfigKey((int)$carrier['id_carrier']));
        }

        return parent::uninstall();
    }

    protected function getConfigKey($id_carrier)
    {
        return 'AHSCZ_PREFIXES_' . (int)$id_carrier;
    }

    /**
     * Hook folosit în checkout pentru a filtra lista carrier-elor
     */
    public function hookFilterCarrierList($params)
    {
        if (!isset($params['carriers']) || !is_array($params['carriers'])) {
            return;
        }

        $carriers = $params['carriers'];

        // obținem codul poștal al adresei de livrare
        $postcode = '';

        if (isset($params['address']) && Validate::isLoadedObject($params['address'])) {
            $postcode = (string)$params['address']->postcode;
        } elseif ($this->context->cart && $this->context->cart->id_address_delivery) {
            $address = new Address((int)$this->context->cart->id_address_delivery);
            if (Validate::isLoadedObject($address)) {
                $postcode = (string)$address->postcode;
            }
        }

        // dacă nu avem încă un cod poștal (user-ul nu a completat adresa) -> nu filtrăm
        if ($postcode === '') {
            return $carriers;
        }

        foreach ($carriers as $k => $carrier) {
            $id_carrier = (int)$carrier['id'];

            $prefixes = trim((string)Configuration::get($this->getConfigKey($id_carrier)));

            // dacă nu e setat nimic pentru carrier -> fără restricții
            if ($prefixes === '') {
                continue;
            }

            $ok = false;
            $list = preg_split('/[;,]+/', $prefixes);

            foreach ($list as $prefix) {
                $prefix = trim($prefix);
                if ($prefix === '') {
                    continue;
                }

                // verificăm dacă codul poștal începe cu prefixul dat
                if (strpos($postcode, $prefix) === 0) {
                    $ok = true;
                    break;
                }
            }

            // dacă NU se potrivește niciun prefix -> scoatem carrier-ul din listă
            if (!$ok) {
                unset($carriers[$k]);
            }
        }

        return $carriers;
    }

    /**
     * Pagina de configurare din BO
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitAhscarrierzip')) {
            $carriers = Carrier::getCarriers(
                $this->context->language->id,
                false,
                false,
                false,
                null,
                Carrier::ALL_CARRIERS
            );

            foreach ($carriers as $carrier) {
                $id = (int)$carrier['id_carrier'];
                $value = Tools::getValue('prefix_' . $id);
                Configuration::updateValue($this->getConfigKey($id), $value);
            }

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        $carriers = Carrier::getCarriers(
            $this->context->language->id,
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );

        $prefixes = [];
        foreach ($carriers as $carrier) {
            $id = (int)$carrier['id_carrier'];
            $prefixes[$id] = Configuration::get($this->getConfigKey($id));
        }

        $this->context->smarty->assign([
            'carriers'            => $carriers,
            'prefixes'            => $prefixes,
            'form_action'         => AdminController::$currentIndex . '&configure=' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'module_display_name' => $this->displayName,
        ]);


        return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
