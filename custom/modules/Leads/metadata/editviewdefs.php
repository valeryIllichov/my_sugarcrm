<?php
$viewdefs ['Leads'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'hidden' => 
        array (
          0 => '<input type="hidden" name="prospect_id" value="{if isset($smarty.request.prospect_id)}{$smarty.request.prospect_id}{else}{$bean->prospect_id}{/if}">',
          1 => '<input type="hidden" name="account_id" value="{if isset($smarty.request.account_id)}{$smarty.request.account_id}{else}{$bean->account_id}{/if}">',
          2 => '<input type="hidden" name="contact_id" value="{if isset($smarty.request.contact_id)}{$smarty.request.contact_id}{else}{$bean->contact_id}{/if}">',
          3 => '<input type="hidden" name="opportunity_id" value="{if isset($smarty.request.opportunity_id)}{$smarty.request.opportunity_id}{else}{$bean->opportunity_id}{/if}">',
        ),
        'buttons' => 
        array (
          0 => 'SAVE',
          1 => 'CANCEL',
        ),
      ),
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'javascript' => '<script type="text/javascript" language="Javascript">function copyAddressRight(form)  {ldelim} form.alt_address_street.value = form.primary_address_street.value;form.alt_address_city.value = form.primary_address_city.value;form.alt_address_state.value = form.primary_address_state.value;form.alt_address_postalcode.value = form.primary_address_postalcode.value;form.alt_address_country.value = form.primary_address_country.value;return true; {rdelim} function copyAddressLeft(form)  {ldelim} form.primary_address_street.value =form.alt_address_street.value;form.primary_address_city.value = form.alt_address_city.value;form.primary_address_state.value = form.alt_address_state.value;form.primary_address_postalcode.value =form.alt_address_postalcode.value;form.primary_address_country.value = form.alt_address_country.value;return true; {rdelim} </script>',
    ),
    'panels' => 
    array (
      'lbl_contact_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'first_name',
            'customCode' => '{html_options name="salutation" options=$fields.salutation.options selected=$fields.salutation.value}&nbsp;<input name="first_name" size="25" maxlength="25" type="text" value="{$fields.first_name.value}">',
            'label' => 'LBL_FIRST_NAME',
            'tabindex' => '1',
          ),
          1 => 
          array (
            'name' => 'last_name',
            'displayParams' => 
            array (
              'required' => true,
            ),
            'label' => 'LBL_LAST_NAME',
            'tabindex' => '2',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'title',
            'label' => 'LBL_TITLE',
            'tabindex' => '3',
          ),
          1 => 
          array (
            'name' => 'department',
            'label' => 'LBL_DEPARTMENT',
            'tabindex' => '4',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'leadopp_c',
            'label' => 'LBL_LEADOPP',
            'tabindex' => '5',
          ),
          1 => 
          array (
            'name' => 'lead_opp_perc_c',
            'label' => 'LBL_LEAD_OPP_PERC',
            'tabindex' => '6',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'account_name',
            'type' => 'varchar',
            'validateDependency' => false,
            'customCode' => '<input name="account_name" {if ($fields.converted.value == 1)}disabled="true"{/if} size="30" maxlength="255" type="text" value="{$fields.account_name.value}">',
            'label' => 'LBL_ACCOUNT_NAME',
            'tabindex' => '7',
          ),
          1 => 
          array (
            'name' => 'dealertype_c',
            'label' => 'LBL_DEALERTYPE',
            'tabindex' => '8',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'region_c',
            'label' => 'LBL_REGION_C',
            'tabindex' => '9',
          ),
          1 => 
          array (
            'name' => 'location_c',
            'label' => 'LBL_LOCATION_C',
            'tabindex' => '10',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'phone_work',
            'label' => 'LBL_OFFICE_PHONE',
            'tabindex' => '11',
          ),
          1 => 
          array (
            'name' => 'phone_mobile',
            'label' => 'LBL_MOBILE_PHONE',
            'tabindex' => '12',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'phone_other',
            'label' => 'LBL_OTHER_PHONE',
            'tabindex' => '13',
          ),
          1 => 
          array (
            'name' => 'phone_fax',
            'label' => 'LBL_FAX_PHONE',
            'tabindex' => '14',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'lead_source',
            'label' => 'LBL_LEAD_SOURCE',
            'tabindex' => '15',
          ),
          1 => 
          array (
            'name' => 'status',
            'label' => 'LBL_STATUS',
            'tabindex' => '16',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'lead_source_description',
            'displayParams' => 
            array (
              'rows' => 4,
              'cols' => 40,
            ),
            'label' => 'LBL_LEAD_SOURCE_DESCRIPTION',
            'tabindex' => '17',
          ),
          1 => 
          array (
            'name' => 'status_description',
            'displayParams' => 
            array (
              'rows' => 4,
              'cols' => 40,
            ),
            'label' => 'LBL_STATUS_DESCRIPTION',
            'tabindex' => '18',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'loyalty_c',
            'label' => 'LBL_LOYALTY',
            'tabindex' => '19',
          ),
          1 => 
          array (
            'name' => 'decision_c',
            'label' => 'LBL_DECISION',
            'tabindex' => '20',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
            'tabindex' => '21',
          ),
          1 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
            'tabindex' => '22',
          ),
        ),
      ),
      'lbl_email_addresses' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'email1',
            'label' => 'LBL_EMAIL_ADDRESS',
            'tabindex' => '23',
          ),
        ),
      ),
      'lbl_address_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'primary_address_street',
            'hideLabel' => true,
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'primary',
              'rows' => 2,
              'cols' => 30,
              'maxlength' => 150,
            ),
            'label' => 'LBL_PRIMARY_ADDRESS_STREET',
            'tabindex' => '24',
          ),
          1 => 
          array (
            'name' => 'alt_address_street',
            'hideLabel' => true,
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'alt',
              'copy' => 'primary',
              'rows' => 2,
              'cols' => 30,
              'maxlength' => 150,
            ),
            'label' => 'LBL_ALT_ADDRESS_STREET',
            'tabindex' => '25',
          ),
        ),
      ),
      'lbl_panel1' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'nickname_c',
            'label' => 'LBL_NICKNAME',
            'tabindex' => '26',
          ),
          1 => 
          array (
            'name' => 'birthdate_c',
            'label' => 'LBL_BIRTHDATE',
            'tabindex' => '27',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'hobbies_c',
            'label' => 'LBL_HOBBIES',
            'tabindex' => '28',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'spouse_c',
            'label' => 'LBL_SPOUSE',
            'tabindex' => '29',
          ),
          1 => 
          array (
            'name' => 'anniversary_c',
            'label' => 'LBL_ANNIVERSARY',
            'tabindex' => '30',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'children_c',
            'label' => 'LBL_CHILDREN',
            'tabindex' => '31',
          ),
        ),
      ),
    ),
  ),
);
?>
