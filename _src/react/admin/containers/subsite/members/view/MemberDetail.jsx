import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';
import PlanList from './subscription/List';

import { toast } from 'react-toastify';

export default class MemberDetail extends Component {

	constructor(props) {
		super(props);

		this.assign_membership = React.createRef();

		this.state = {
			member : {},
			loading : true,
			error : false,
			memberships : [],
			plans : {},
			statuses : [],
			new_sub : {
				membership : 0,
				object : {}
			},
			selected_access : 'date',
			id : this.props.id
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'members-detail-toast'});
	}

	async componentDidMount() {
		Promise.all([this.load_member(), this.load_memberships(), this.load_member_plans()]);
	}

	componentDidUpdate() {
		window.hammock.helper.bind_date_range();
	}

	handleAssignMembership(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.assign_membership.current),
			$button = $form.find('button'),
			form = $form.serialize(),
			$btn_txt = $button.text();

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'members/plan/create', form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success' );
					setTimeout(function(){
						UIkit.modal(jQuery('#hammock-add-subscription')).hide();
						self.load_member_plans();
					}, 1000);
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( self.props.hammock.error, 'error' );
			}
		);
	}

	load_member = async () => {
		const id = this.state.id;
		this.fetchWP.get( 'members/get?id=' + id )
			.then( (json) => this.setState({
				member : json,
				loading : false,
				error : false,
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( self.props.hammock.error, 'error' );
			}
		);
	}

	refresh_member_plans = async () => {
		Promise.all([ this.load_memberships(), this.load_member_plans()]);
	}

	load_member_plans = async () => {
		const id = this.state.id;
		this.fetchWP.get( 'members/get/plans?id=' + id )
			.then( (json) => this.setState({
				plans : json
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( self.props.hammock.error, 'error' );
			}
		);
	}

	load_memberships = async () => {
		const id = this.state.id;
		this.fetchWP.get( 'memberships/list_simple?member=' + id )
			.then( (json) => this.setState({
				memberships : json,
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( self.props.hammock.error, 'error' );
			}
		);
	}

	list_status = async () => {
		this.fetchWP.get( 'members/list/status' )
			.then( (json) => this.setState({
				statuses : json,
			}), (err) => this.notify( self.props.hammock.error, 'error' )
		);
	}

	membership_detail = async ( id ) => {
		this.fetchWP.get( 'memberships/get?id=' + id )
			.then( (json) => this.setState({ new_sub : {
				membership : id,
				object : json
			} }), (err) => this.notify( self.props.hammock.error, 'error' )
		);
	}

	toggle_membership_fields( target, selected ) {
		this.setState({ new_sub : {
			membership : selected,
			object : false
		} });
		if ( selected > 0 ) {
			this.membership_detail( selected );
		}
	}

	toggle_trial_enabled( checked ) {
		var access = document.getElementsByClassName('hammock-membership-member-access');
		access = access[0];
		if ( checked ) {
			access.style.display = "none";
		} else {
			access.style.display = "block";
		}
	}

	member_access_type( e ) {
		var value = e.currentTarget.value;
		this.setState( {selected_access : value});
	}

	delete(event) {
		event.preventDefault();
		var $button = jQuery(event.target),
			$btn_txt = $button.text(),
			id = $button.attr('data-id'),
			prompt = $button.attr('data-prompt'),
			helper = window.hammock.helper,
			error = this.props.hammock.error,
			fetchWP = this.fetchWP;
		
		helper.confirm( prompt, 'warning', function() {
			//continue
			$button.attr('disabled', 'disabled');
			$button.html("<div uk-spinner></div>");
			fetchWP.post( 'members/delete', { member : id } )
				.then( (json) => {
					if ( json.status ) {
						helper.notify( json.message, 'success');
						window.location.hash = "#/";
					} else {
						helper.notify( json.message, 'warning' );
					}
					$button.removeAttr('disabled');
					$button.html($btn_txt);
				}, (err) => {
					$button.removeAttr('disabled');
					$button.html($btn_txt);
					helper.notify( error, 'error' );
				}
			);
		});
	}

	addSubscription( hammock, strings ) {
		const selected_membership = ( this.state.new_sub.membership > 0 && typeof this.state.new_sub.object !== "undefined" ) ? this.state.new_sub.object : false;
		const selected_access = this.state.selected_access;
		return(
			<div id="hammock-add-subscription" className="uk-flex-top" uk-modal="">
				<div className="uk-modal-dialog uk-margin-auto-vertical">
					<div className="uk-modal-header">
						<h2 className="uk-modal-title">{strings.edit.details.subscription.create.modal.title}</h2>
					</div>
					<button className="uk-modal-close-default" type="button" uk-close=""></button>
					<div className="uk-modal-body">
						<form className="uk-form-stacked" onSubmit={this.handleAssignMembership.bind(this)} ref={this.assign_membership}>
							<InputUI name={`member`} type={`hidden`} value={this.state.member.id}/>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.edit.details.subscription.create.modal.membership}</legend>
								<div className="uk-form-controls">
									<DropDownUI name={`membership`} values={this.state.memberships} class_name={`hammock-membership-list`} action={this.toggle_membership_fields.bind(this)}/>
								</div>
							</div>
							{selected_membership &&
								<React.Fragment>
									{selected_membership.trial_enabled &&
										<React.Fragment>
											<div className="uk-margin">
												<div className="uk-form-controls">
													<div className="hammock-input">
														<SwitchUI name={`enable_trial`} class_name={`enable_trial`} title={strings.edit.details.subscription.create.modal.enable_trial + selected_membership.trial_period + ' ' + selected_membership.trial_duration_text} value={`1`} action={this.toggle_trial_enabled.bind(this)} />
													</div>
												</div>
											</div>
										</React.Fragment>
									}
									<div className="hammock-membership-member-access">
										<div className="uk-margin">
											<legend className="uk-form-label">{strings.edit.details.subscription.create.modal.grant.title}</legend>
											<div className="uk-margin uk-grid-small uk-child-width-auto uk-grid">
												<label><input className="uk-radio" type="radio" name="access" value="date" onChange={this.member_access_type.bind(this)} onClick={this.member_access_type.bind(this)} checked={selected_access === 'date'} /> {strings.edit.details.subscription.create.modal.grant.date}</label>
												<label><input className="uk-radio" type="radio" name="access" value="permanent" onChange={this.member_access_type.bind(this)} onClick={this.member_access_type.bind(this)} checked={selected_access === 'permanent'}/> {strings.edit.details.subscription.create.modal.grant.permanent}</label>
												<label><input className="uk-radio" type="radio" name="access" value="invoice" onChange={this.member_access_type.bind(this)} onClick={this.member_access_type.bind(this)} checked={selected_access === 'invoice'}/> {strings.edit.details.subscription.create.modal.grant.invoice}</label>
											</div>
										</div>
										<div className="uk-margin" style={{display: ( selected_access === 'date' ? 'block' : 'none' )}}>
											<legend className="uk-form-label">{strings.labels.start_date}</legend>
											<div className="uk-form-controls">
												<InputUI name={`membership_start`} class_name={`hammock-from-date`} placeholder={strings.labels.start_date}/>
											</div>
										</div>
										<div className="uk-margin" style={{display: ( selected_access === 'date' ? 'block' : 'none' )}}>
											<legend className="uk-form-label">{strings.labels.end_date}</legend>
											<div className="uk-form-controls">
												<InputUI name={`membership_end`} class_name={`hammock-to-date`} placeholder={strings.labels.end_date}/>
											</div>
										</div>
									</div>
									<div className="uk-margin ">
										<button className="uk-button uk-button-primary uk-button-small save-button">{hammock.common.buttons.save}</button>
									</div>
								</React.Fragment>
							}
						</form>
					</div>
				</div>
			</div>
		)
	}

	memberDetail() {
		var hammock = this.props.hammock;
		const strings = hammock.strings;
		return (
			<React.Fragment>
				{this.state.member.id > 0 ? (
					<div>
						<div uk-grid="" className="uk-background-default uk-padding-small uk-margin-remove-left">
							<div className="uk-width-1-3 uk-padding-remove-left">
								<img src={this.state.member.user_info.picture} title={this.state.member.user_info.name}/>
							</div>
							<div className="uk-width-expand">
								<ul className="uk-list">
									<li><strong>{this.state.member.user_info.name}</strong></li>
									<li><a href={"mailto:" + this.state.member.user_info.email}>{this.state.member.user_info.email}</a></li>
									<li>{strings.labels.member_id} : <code>{this.state.member.member_id}</code></li>
									<li>{strings.edit.details.status} : {this.state.member.enabled ? hammock.common.status.enabled : hammock.common.status.disabled}</li>
									<li>{strings.edit.details.since} : {this.state.member.date_created}</li>
									<li><a href={this.state.member.user_edit_url} target="_blank" className="uk-button uk-button-default uk-button-small">{strings.labels.profile_url}</a></li>
									<li><a href="#" data-id={this.state.member.id} data-prompt={strings.edit.details.delete.prompt} onClick={this.delete.bind(this)} className="uk-button uk-button-danger uk-button-small">{strings.edit.details.delete.title}</a></li>
								</ul>
							</div>
						</div>
						<div className="uk-background-default uk-padding-small uk-margin-small-top hammock-margin-left-negative-40 uk-margin-remove-left">
							<h4 className="uk-heading-divider">{strings.edit.details.subscription.title}
							{this.state.plans.total > 0 &&
								<a className="uk-margin-small-left uk-button uk-button-primary uk-button-small" href="#hammock-add-subscription" uk-toggle="">{strings.edit.details.subscription.create.title}</a>
							}
							</h4>
							{this.state.plans.total > 0 ? (
									<PlanList plans={this.state.plans} hammock={hammock} action={this.refresh_member_plans.bind(this)}/>
								) : (
									<div className="uk-text-center">
										<a className="uk-button uk-button-primary uk-button-small" href="#hammock-add-subscription" uk-toggle="">{strings.edit.details.subscription.create.title}</a>
									</div>
								)
							}
						</div>
						{this.addSubscription( hammock, strings )}
					</div>
				) : (
					<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
						<h3 className="uk-text-center">{strings.edit.not_found}</h3>
					</div>
				) }
			</React.Fragment>
		)
	}

	render() {
		if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{this.props.hammock.error}</h3>
				)
			} else {
				return this.memberDetail();
			}
		}
	}
}

MemberDetail.propTypes = {
	hammock: PropTypes.object
};