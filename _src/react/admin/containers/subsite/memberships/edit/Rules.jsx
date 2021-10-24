import React, { PureComponent } from 'react';

import fetchWP from 'utils/fetchWP'
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class Rules extends PureComponent {
	constructor(props) {
		super(props);
		this.membership_rules = React.createRef();
		this.state = {
            membership: this.props.membership
        };
		
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	notify(type, message) {
		toast[type](message, {toastId: 'memberships-edit-rules-toast'});
	}

	handleSubmit(event) {

	}

	render() {
		const membership = this.state.membership;
		var hammock = this.props.hammock;
		var strings = hammock.strings;
		return (
			<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit} ref={this.membership_rules}>

			</form>
		)
	}
}