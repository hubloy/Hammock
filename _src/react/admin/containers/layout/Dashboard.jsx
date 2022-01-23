import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var title = typeof this.props.title !== 'undefined' ? this.props.title : this.props.hammock.common.string.title,
			button = typeof this.props.button !== 'undefined' ? this.props.button : '';
		return (
			<div className='hammock-admin-content-container'>
				<div className='hammock-menu-area'>
					<h1 className="hammock-menu-area-title">{title}</h1>
					<ul className="hammock-menu-area-items uk-iconnav uk-iconnav-vertical">
						<li className="hammock-menu-area-item uk-active">
							<a>
								<span className="hammock-menu-area-item-icon" uk-icon="home"></span>
								<span className="hammock-menu-area-item-name">Home</span>
							</a>
						</li>
						<li className="hammock-menu-area-item">
							<a>
								<span className="hammock-menu-area-item-icon" uk-icon="settings"></span>
								<span className="hammock-menu-area-item-name">Home</span>
							</a>
						</li>
						<li className="hammock-menu-area-item">
							<a>
								<span className="hammock-menu-area-item-icon" uk-icon="home"></span>
								<span className="hammock-menu-area-item-name">Home</span>
							</a>
						</li>
					</ul>
					<div className='hammock-menu-area-toggle hide-if-no-js'>
						<div className='hammock-menu-area-toggle-icon'>
							<span className="uk-icon-button hammock-menu-area-toggle-icon-maximized" uk-icon="chevron-left"></span>
						</div>
						<div className='hammock-menu-area-toggle-label'>
							Minimize Navigation
						</div>
					</div>
				</div>
				<div className='hammock-content-area'>
					<div className="uk-container hammock-main-header uk-background-default uk-width-1-1 uk-padding-small" uk-sticky="top: #wpadminbar; offset: 30">
						<div className="uk-grid-collapse uk-child-width-expand@s uk-grid-small" uk-grid="">
							<div><h2 className='hammock-main-header-title'>{title}</h2></div>
							<div className="uk-text-right uk-margin-small-right uk-margin-small-top">{button}</div>
						</div>
					</div>
					<div className="hammock-container">
						<div className="uk-width-auto">
							{this.props.children}
						</div>
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
			</div>
		)
	}
}

Dashboard.propTypes = {
	hammock: PropTypes.object
};