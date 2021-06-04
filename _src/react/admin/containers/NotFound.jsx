import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'

export default class NotFound extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				<h2 className="uk-text-center uk-heading-divider">{this.props.hammock.common.string.not_found}</h2>
			</Dashboard>
		)
	}
}

NotFound.propTypes = {
	hammock: PropTypes.object
};