import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';

import Card from './Card';

import { InputUI, Canvas } from 'ui/admin/form'

import { toast } from 'react-toastify';

export default class List extends PureComponent {

	constructor(props) {
		super(props);

		this.addon_side_content = React.createRef();

		this.state = {
			items: [],
			loading : true,
			error : false,
		};

		this.handleUpdateAddonSetting = this.handleUpdateAddonSetting.bind(this);
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	async componentDidMount() {
        this.loadPage();
	}

	notify(type, message) {
		toast[type](message, {toastId: 'addon-list-toast'});
	}


	handleUpdateAddonSetting( event ) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.addon_side_content.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'addons/settings/update', form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success' );
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( self.props.hammock.error, 'error' )
			}
		);
	}
	
	loadPage = async () => {
		this.fetchWP.get( 'addons/list' )
			.then( (json) => this.setState({
				items : json,
				loading : false,
				error : false,
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	render() {
		const { items } = this.state;
		var hammock = this.props.hammock;
		if ( this.state.loading) {
            return (
                <div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
                    <span className="uk-text-center" uk-spinner="ratio: 3"></span>
                </div>
            )
        } else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hammock.error}</h3>
				)
			} else {
				return (
					<React.Fragment>
						<div uk-filter="target: .addon-filter">
							<nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
								<div className="uk-navbar-left">
									<ul className="uk-navbar-nav hammock-navbar">
										<li className="uk-active" uk-filter-control=""><span>{hammock.common.general.all}</span></li>
										<li uk-filter-control=".enabled"><span>{hammock.common.status.enabled}</span></li>
										<li uk-filter-control=".disabled"><span>{hammock.common.status.disabled}</span></li>
									</ul>
								</div>
							</nav>

							<ul className="addon-filter uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-5@l uk-text-center" uk-grid="">
								{Object.keys(items).map(item =>
									<React.Fragment key={item}>
										<Card hammock={hammock} id={item} item={items[item]} key={item}/>
									</React.Fragment>
								)}
							</ul>

						</div>
						<Canvas canvas_id={`addons-settings`}>
							<h3 className="addon-title">{hammock.common.status.loading}</h3>
							<div className="uk-container uk-padding-remove">
								<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleUpdateAddonSetting} ref={this.addon_side_content}>
									<InputUI name={`id`} class_name={`addon_id`} type={`hidden`} value=''/>
									<div className="addon-content">

									</div>
									<div className="uk-margin ">
										<button className="uk-button uk-button-primary">{hammock.common.buttons.update}</button>
									</div>
								</form>
							</div>
						</Canvas>
					</React.Fragment>
				)
			}
		}
	}
};