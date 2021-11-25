import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import List from './addons/List';

export default class Addon extends Component {

	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				<List hammock={this.props.hammock}/>
			</Dashboard>
		)
	}
}

Addon.propTypes = {
	hammock: PropTypes.object
};