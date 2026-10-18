{**
 * Journal Badges — frontend strip (moved above footer by JS)
 * Author : Edi Suherlan | audhighasu.com
 *}
<section class="jb-badges" id="jbBadges" aria-label="{$jbAria|escape}" hidden>
	<div class="jb-badges__inner">
		{if $jbStripTitle}
			<p class="jb-badges__title">{$jbStripTitle|escape}</p>
		{/if}
		<ul class="jb-badges__list">
			{foreach from=$jbBadges item=badge}
				<li class="jb-badges__item">
					{if $badge.url}
						<a class="jb-badge jb-badge--{$badge.kind|escape}" href="{$badge.url|escape}" target="_blank" rel="noopener noreferrer" title="{$badge.note|escape}">
							<span class="jb-badge__mark" aria-hidden="true"></span>
							<span class="jb-badge__label">{$badge.label|escape}</span>
						</a>
					{else}
						<span class="jb-badge jb-badge--{$badge.kind|escape}" title="{$badge.note|escape}">
							<span class="jb-badge__mark" aria-hidden="true"></span>
							<span class="jb-badge__label">{$badge.label|escape}</span>
						</span>
					{/if}
				</li>
			{/foreach}
		</ul>
	</div>
</section>
<script>
(function () {
	var el = document.getElementById('jbBadges');
	if (!el) return;
	var footer = document.querySelector('.pkp_structure_footer_wrapper');
	if (footer && footer.parentNode) {
		footer.parentNode.insertBefore(el, footer);
	}
	el.removeAttribute('hidden');
})();
</script>
