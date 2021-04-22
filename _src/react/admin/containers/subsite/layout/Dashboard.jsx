import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Header } from '../../../ui/admin/elements/Header'

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<React.Fragment>
				{/* <Header hammock={this.props.hammock}/> */}
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