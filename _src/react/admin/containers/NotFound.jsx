import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'

export default class NotFound extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Dashboard hubloy_membership={this.props.hubloy_membership}>
				<h2 className="uk-text-center uk-heading-divider">{this.props.hubloy_membership.common.string.not_found}</h2>
			</Dashboard>
		)
	}
}

NotFound.propTypes = {
	hubloy_membership: PropTypes.object
};