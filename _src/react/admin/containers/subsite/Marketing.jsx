import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'


export default class Marketing extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hammock = this.props.hammock;
		return (
			<Dashboard hammock={hammock}>
				
			</Dashboard>
		)
	}
}

Marketing.propTypes = {
	hammock: PropTypes.object
};