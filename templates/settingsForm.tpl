{**
 * Journal Badges — settings
 * Author : Edi Suherlan | audhighasu.com
 *}
<script>
	$(function() {ldelim}
		$('#journalBadgesSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="journalBadgesSettingsForm" method="post" action="{url router=PKP\core\PKPApplication::ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="journalBadgesSettingsFormNotification"}

	<div id="description" class="description">{translate key="plugins.generic.journalBadges.settings.description"}</div>

	{fbvFormArea id="journalBadgesSettingsArea"}
		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="showFrontend" name="showFrontend" checked=$showFrontend label="plugins.generic.journalBadges.settings.showFrontend" translate=true}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.journalBadges.settings.stripTitle" for="stripTitle"}
			{fbvElement type="text" id="stripTitle" value=$stripTitle maxlength=120}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.journalBadges.settings.badgesJson" for="badgesJson" description="plugins.generic.journalBadges.settings.badgesJson.description"}
			{fbvElement type="textarea" id="badgesJson" value=$badgesJson rich=false rows=16}
		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="restoreDummy" name="restoreDummy" label="plugins.generic.journalBadges.settings.restoreDummy" translate=true}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons}
</form>
