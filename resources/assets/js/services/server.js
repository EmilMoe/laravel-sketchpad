import Queue from './server/queue';
import Request from './server/request';
import _ from 'underscore'

function Server()
{
	// setup base route
	this.base   = $('meta[name="route"]').attr('content');
	this.queue  = new Queue();
}

Server.prototype =
{

		base        : '',

		queue       : null,

	// ------------------------------------------------------------------------------------------------
	// methods

		/**
		 * Runs a sketchpad method and returns the result
		 *
		 * @param 	{Object}	method		A Method object with route and params properties
		 * @param 	{Function}	done
		 * @param 	{Function}	fail
		 * @param 	{Function}	always
		 * @returns {Request}
		 */
		run(method, done, fail, always)
		{
			const url	= this.getRunUrl(method);
			let data	= method.params.map(param => _.pick(param, 'name', 'type', 'value'));
			if (method.runIf && method.runState)
			{
				data.push({name: method.runIf, type:'boolean', value:true})
				method.runState = false;
			}
			return this.queue.add(new Request(url, data, done, fail, always));
		},

		/**
		 * Opens a sketchpad route in a new window
		 *
		 * @param route
		 * @param data
		 */
		open(route, data)
		{
			const request = new Request(route, data);
			window.open(request.url);
		},

		/**
		 * Requests information from the server
		 *
		 * Mainly used for :page/
		 *
		 * @param 	{string}	path			The partial route, from '/sketchpad/' onwards
		 * @param	{Object}	[data]          Optional data to pass with the request
		 * @param	{Function}	[done]          An optional onLoad handler
		 * @returns {Promise}
		 */
		load(path, data, done)
		{
			const url = this.getUrl(path);
			return $.get(url, data, done);
		},

		loadController(route = '')
		{
			return this.load('api/load/' + route);
		},

		post(path, data, done)
		{
			const url = this.getUrl(path);
			return $.post(url, data, done);
		},

		submit(method, data, done)
		{
			const url = this.getRunUrl(method);
			return $.post(url, data, done);
		},

		validatePath(path)
		{
			return this.load('api/path', {path:path})
		},

		getRunUrl(method)
		{
			return this.getUrl('api/run/' + method.route);
		},

		getUrl(path)
		{
			return this.base + path;
		}
};

export default new Server;




