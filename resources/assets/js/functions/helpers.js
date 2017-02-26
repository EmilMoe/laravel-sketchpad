import settings from '../state/settings.js';

export default
{
	getMethodLabel:function(method)
	{
		return settings.ui.humanizeText
			? this.humanize(method.label)
			: method.label + '()';
	},

	humanize:function(input)
	{
		return input
			.replace(/_/g, ' ')
			.replace(/([a-z])([A-Z0-9])/g, '$1 $2')
			.toLowerCase();
	}

};
