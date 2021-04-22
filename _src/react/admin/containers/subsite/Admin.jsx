import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from './layout/Dashboard'

export default class Admin extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				Hello
			</Dashboard>
		)
	}
}

Admin.propTypes = {
	hammock: PropTypes.object
};