<div class="crm-block crm-form-block crm-expertapplication-form-block">

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

<div id="help">{ts}Enter the settings for completing the registering of a new expert. Select the Drupal User role which is granted to the new expert when he or she has to enter his/her PUM CV.{/ts}</div>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">
        {$form.$elementName.html}
    </div>
    <div class="clear"></div>
  </div>
{/foreach}

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

</div>
