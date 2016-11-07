{if $exception|default:null}
	<div class="alert alert-danger">
		{if $exception->getCode()|default:'0'}
			<h4>
				<i class="icon fa fa-ban"></i>
				{$exception->getCode()|default:''}
			</h4>
		{/if}

		<p>{$exception->getMessage()|default:''}</p>

		{if $exception->getCode()|default:'0' == '0'}
			<hr />
			<div>
				{$exception->getTraceAsString()|nl2br nofilter}
			</div>
		{/if}
	</div>
{/if}