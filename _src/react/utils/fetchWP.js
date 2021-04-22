import fetch from 'isomorphic-fetch';

const methods = [
	'get',
	'post',
	'put',
	'delete',
];

export default class fetchWP {
	constructor(options = {}) {
		this.options = options;

		if (!options.api_url)
			throw new Error('ajax_url option is required');

		if (!options.api_nonce)
			throw new Error('api_nonce option is required');

		methods.forEach(method => {
			this[method] = this._setup(method);
		});
	}

	_setup(method) {
		return (endpoint = '/', data = false, form_post = false) => {
			let fetchObject = {
				credentials: 'same-origin',
				method: method,
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-WP-Nonce': this.options.api_nonce,
				}
			};

			if (data) {
				fetchObject.body = JSON.stringify(data);
			}

			if ( form_post ) {
				fetchObject.headers =  {
					'Accept': 'application/json',
					'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
					'X-WP-Nonce': this.options.api_nonce,
				};
				fetchObject.body = data;
			}

			return fetch(this.options.api_url + endpoint, fetchObject)
				.then(response => {
					return response.json().then(json => {
						return response.ok ? json : Promise.reject(json);
					});
				});
		}
	}
}