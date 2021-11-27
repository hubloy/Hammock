import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var title = typeof this.props.title !== 'undefined' ? this.props.title : this.props.hammock.common.string.title;
		return (
			<React.Fragment>
				<div className="uk-container hammock-main-header uk-background-default uk-width-1-1 uk-padding-small" uk-sticky="top: #wpadminbar; offset: 30">
					<h2>{title}</h2>
				</div>
				<div className="hammock-container">
					<div className="uk-width-auto">
						{this.props.children}
					</div>
				</div>
				<ToastContainer
					position="top-center"
					autoClose={1200}
					hideProgressBar={false}
					newestOnTop={false}
					closeOnClick
					rtl={false}
					pauseOnFocusLoss
					draggable={false}
					pauseOnHover
				/>
			</React.Fragment>
		)
	}
}

Dashboard.propTypes = {
	hammock: PropTypes.object
};