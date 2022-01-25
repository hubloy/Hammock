import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default class Dashboard extends Component {
	constructor(props) {
		super(props);

		this.state = {
			minimized : false,
		};

		this.toggleNavigation = this.toggleNavigation.bind(this);
	}

	async componentDidMount() {
		var minimized = window.localStorage.getItem( '_hammock_admin_menu' );
		if ( minimized && minimized == 'minimized' ) {
			this.setState( { minimized : true });
		}
	}

	toggleNavigation() {
		var minimized = this.state.minimized,
			newState = !minimized;
		if ( newState ) {
			window.localStorage.setItem( '_hammock_admin_menu', 'minimized' );
		} else {
			window.localStorage.removeItem( '_hammock_admin_menu' );
		}
		this.setState( { minimized : newState });
	}

	render() {
		var hammock = this.props.hammock,
			title = typeof this.props.title !== 'undefined' ? this.props.title : hammock.common.string.title,
			section = typeof this.props.section !== 'undefined' ? this.props.section : hammock.common.string.section,
			button = typeof this.props.button !== 'undefined' ? this.props.button : '',
			minimized = this.state.minimized;
		return (
			<div className={'hammock-admin-content-container' + ( minimized ? ' minimized' : '')}>
				<div className='hammock-menu-area'>
					<h1 className="hammock-menu-area-title"><span uk-icon="home"></span><span className='hammock-menu-area-title-label'>{title}</span></h1>
					<ul className="hammock-menu-area-items uk-iconnav uk-iconnav-vertical">
						<li className="hammock-menu-area-item uk-active">
							<a uk-tooltip="title: Hello World; pos: right">
								<span className="hammock-menu-area-item-icon" uk-icon="home"></span>
								<span className="hammock-menu-area-item-name">{section}</span>
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
					<div className='hammock-menu-area-toggle hide-if-no-js' onClick={this.toggleNavigation}>
						<div className='hammock-menu-area-toggle-icon'>
							<span className="hammock-menu-area-toggle-icon-maximized" uk-icon="chevron-left"></span>
							<span className="hammock-menu-area-toggle-icon-minimized" uk-icon="chevron-right"></span>
						</div>
						<div className='hammock-menu-area-toggle-label'>
							{hammock.nav.minimize}
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