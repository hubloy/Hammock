import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import List from './addons/List';

export default class Addon extends Component {

	render() {
		return (
			<Dashboard hubloy_membership={this.props.hubloy_membership}>
				<List hubloy_membership={this.props.hubloy_membership}/>
			</Dashboard>
		)
	}
}

Addon.propTypes = {
	hubloy_membership: PropTypes.object
};