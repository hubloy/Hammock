import React, { Component } from 'react';
import PropTypes from 'prop-types';

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<React.Fragment>
				<div className="hammock-container">
					<div className="uk-width-auto">
						{this.props.children}
					</div>
				</div>
			</React.Fragment>
		)
	}
}

Dashboard.propTypes = {
	hammock: PropTypes.object
};