import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import fetchWP from 'utils/fetchWP';
import { toast } from 'react-toastify';

export default class MemberDetail extends Component {

	constructor(props) {
		super(props);
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
		this.delete = this.delete.bind(this);
	}

	delete(event) {
		event.preventDefault();
		var $button = jQuery(event.target),
			$btn_txt = $button.text(),
			id = $button.attr('data-id'),
			prompt = $button.attr('data-prompt'),
			helper = window.hubloy_membership.helper,
			self = this,
			error = self.props.hubloy_membership.error,
			fetchWP = self.fetchWP;
		
		helper.confirm( prompt, 'warning', function() {
			//continue
			$button.attr('disabled', 'disabled');
			$button.html("<div uk-spinner></div>");
			fetchWP.post( 'members/delete', { member : id } )
				.then( (json) => {
					if ( json.status ) {
						self.notify( json.message, 'success');
						window.location.hash = "#/";
					} else {
						self.notify( json.message, 'warning' );
					}
					$button.removeAttr('disabled');
					$button.html($btn_txt);
				}, (err) => {
					$button.removeAttr('disabled');
					$button.html($btn_txt);
					self.notify( error, 'error' );
				}
			);
		});
	}

	notify(message,type) {
		toast[type](message, {toastId: 'members-detail-toast'});
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership,
			strings = hubloy_membership.strings,
			member = this.props.member;
		return (
			<div>
				<div uk-grid="" className="uk-background-default uk-padding-small uk-margin-remove-left">
					<div className="uk-width-1-3 uk-padding-remove-left">
						<img src={member.user_info.picture} title={member.user_info.name}/>
					</div>
					<div className="uk-width-expand">
						<ul className="uk-list">
							<li><strong>{member.user_info.name}</strong></li>
							<li><a href={"mailto:" + member.user_info.email}>{member.user_info.email}</a></li>
							<li>{strings.labels.member_id} : <code>{member.member_id}</code></li>
							<li>{strings.edit.details.status} : {member.enabled ? hubloy_membership.common.status.enabled : hubloy_membership.common.status.disabled}</li>
							<li>{strings.edit.details.since} : {member.date_created}</li>
							<li><a href={member.user_edit_url} target="_blank" className="uk-text-primary">{strings.labels.profile_url}</a></li>
							<li><a href="#" data-id={member.id} data-prompt={strings.edit.details.delete.prompt} onClick={this.delete} className="uk-text-danger delete-button">{strings.edit.details.delete.title}</a></li>
							<li><Link to="/" className="uk-button uk-button-default uk-button-small">{strings.edit.back}</Link></li>
						</ul>
					</div>
				</div>
			</div>
		)
	}
}

MemberDetail.propTypes = {
	hubloy_membership: PropTypes.object
};