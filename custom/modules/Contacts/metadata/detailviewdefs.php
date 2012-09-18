<?php
$viewdefs ['Contacts'] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'preForm' => '<form name="vcard" action="index.php"><input type="hidden" name="entryPoint" value="vCard"><input type="hidden" name="contact_id" value="{$fields.id.value}"><input type="hidden" name="module" value="Contacts"></form>',
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 'FIND_DUPLICATES',
          4 => 
          array (
            'customCode' => '<input title="{$APP.LBL_MANAGE_SUBSCRIPTIONS}" class="button" onclick="this.form.return_module.value=\'Contacts\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Subscriptions\'; this.form.module.value=\'Campaigns\';" type="submit" name="Manage Subscriptions" value="{$APP.LBL_MANAGE_SUBSCRIPTIONS}">',
          ),
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
      'includes' => 
      array (
        0 => 
        array (
          'file' => 'modules/Leads/Lead.js',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'full_name',
            'customCode' => '{$fields.full_name.value}&nbsp;&nbsp;<input type="button" class="button" name="vCardButton" value="{$MOD.LBL_VCARD}" onClick="document.vcard.submit();">',
            'label' => 'LBL_NAME',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'title',
            'label' => 'LBL_TITLE',
          ),
          1 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'department',
            'label' => 'LBL_DEPARTMENT',
          ),
          1 => 
          array (
            'name' => 'email1',
            'label' => 'LBL_EMAIL_ADDRESS',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'account_name',
            'label' => 'LBL_ACCOUNT_NAME',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'phone_work',
            'label' => 'LBL_OFFICE_PHONE',
          ),
          1 => 
          array (
            'name' => 'phone_mobile',
            'label' => 'LBL_MOBILE_PHONE',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'phone_home',
            'label' => 'LBL_HOME_PHONE',
          ),
          1 => 
          array (
            'name' => 'phone_other',
            'label' => 'LBL_OTHER_PHONE',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'lead_source',
            'label' => 'LBL_LEAD_SOURCE',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'phone_fax',
            'label' => 'LBL_FAX_PHONE',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'sync_contact',
            'label' => 'LBL_SYNC_CONTACT',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'date_modified',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),
          1 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
            'label' => 'LBL_DATE_ENTERED',
          ),
        ),
        11 => 
        array (
          0 => 
          array (
            'name' => 'loyalty_c',
            'label' => 'LBL_LOYALTY',
          ),
          1 => 
          array (
            'name' => 'decision_c',
            'label' => 'LBL_DECISION',
          ),
        ),
        12 => 
        array (
          0 => 
          array (
            'name' => 'primary_address_street',
            'label' => 'LBL_PRIMARY_ADDRESS',
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'primary',
            ),
          ),
        ),
        13 => 
        array (
          0 => 
          array (
            'name' => 'portal_name',
            'customCode' => '{if $PORTAL_ENABLED}{$fields.portal_name.value}{/if}',
            'customLabel' => '{if $PORTAL_ENABLED}{sugar_translate label="LBL_PORTAL_NAME" module="Contacts"}{/if}',
            'label' => 'LBL_PORTAL_NAME',
          ),
          1 => 
          array (
            'name' => 'portal_active',
            'customCode' => '{if $PORTAL_ENABLED}
	          		         {if strval($fields.portal_active.value) == "1" || strval($fields.portal_active.value) == "yes" || strval($fields.portal_active.value) == "on"}
	          		         {assign var="checked" value="CHECKED"}
                             {else}
                             {assign var="checked" value=""}
                             {/if}
                             <input type="checkbox" class="checkbox" name="{$fields.portal_active.name}" size="{$displayParams.size}" disabled="true" {$checked}>
                             {/if}',
            'customLabel' => '{if $PORTAL_ENABLED}{sugar_translate label="LBL_PORTAL_ACTIVE" module="Contacts"}{/if}',
            'label' => 'LBL_PORTAL_ACTIVE',
          ),
        ),
        14 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
        15 => 
        array (
          0 => 
          array (
            'name' => 'nickname_c',
            'label' => 'LBL_NICKNAME',
          ),
          1 => 
          array (
            'name' => 'birthdate',
            'label' => 'LBL_BIRTHDATE',
          ),
        ),
        16 => 
        array (
          0 => 
          array (
            'name' => 'hobbies_c',
            'label' => 'LBL_HOBBIES',
          ),
        ),
        17 => 
        array (
          0 => 
          array (
            'name' => 'hotbuttons_c',
            'label' => 'LBL_HOTBUTTONS_C',
          ),
        ),
        18 => 
        array (
          0 => 
          array (
            'name' => 'spouse_c',
            'label' => 'LBL_SPOUSE',
          ),
          1 => 
          array (
            'name' => 'anniversary_c',
            'label' => 'LBL_ANNIVERSARY',
          ),
        ),
        19 => 
        array (
          0 => 
          array (
            'name' => 'children_c',
            'label' => 'LBL_CHILDREN',
          ),
        ),
      ),
    ),
  ),
);
?>
