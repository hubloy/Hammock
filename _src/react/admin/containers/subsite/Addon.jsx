import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import List from './addons/List';

export default class Addon extends Component {

	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				<h2 className="uk-text-center uk-heading-divider">{this.props.hammock.common.string.title}</h2>
				<List hammock={this.props.hammock}/>
			</Dashboard>
		)
	}
}

Addon.propTypes = {
	hammock: PropTypes.object
};