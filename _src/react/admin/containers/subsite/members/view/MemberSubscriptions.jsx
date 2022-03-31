import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';
import PlanList from './subscription/List';

import { toast } from 'react-toastify';

export default class MemberSubscriptions extends Component {

	constructor(props) {
		super(props);

		this.assign_membership = React.createRef();

		this.state = {
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
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });

		this.toggle_membership_fields = this.toggle_membership_fields.bind(this);
		this.member_access_type = this.member_access_type.bind(this);
		this.toggle_trial_enabled = this.toggle_trial_enabled.bind(this);
		this.refresh_member_plans = this.refresh_member_plans.bind(this);
		this.handleAssignMembership = this.handleAssignMembership.bind(this);
		this.load_memberships = this.load_memberships.bind(this);
		this.load_member_plans = this.load_member_plans.bind(this);
	}

	notify(message,type) {
		toast[type](message, {toastId: 'members-subscriptions-toast'});
	}

	async componentDidMount() {
		this.refresh_member_plans();
	}

	componentDidUpdate() {
		window.hubloy_membership.helper.bind_date_range();
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
						UIkit.modal(jQuery('#hubloy_membership-add-subscription')).hide();
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
				self.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

	refresh_member_plans () {
		Promise.all([ this.load_memberships(), this.load_member_plans()]);
	}

	load_member_plans () {
		const id = this.state.id;
		this.fetchWP.get( 'members/get/plans?id=' + id )
			.then( (json) => this.setState({
				plans : json
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

	load_memberships () {
		const id = this.state.id;
		this.fetchWP.get( 'memberships/list_simple?member=' + id )
			.then( (json) => this.setState({
				memberships : json,
				loading : false,
				error : false
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

	list_status = async () => {
		this.fetchWP.get( 'members/list/status' )
			.then( (json) => this.setState({
				statuses : json,
			}), (err) => this.notify( self.props.hubloy_membership.error, 'error' )
		);
	}

	membership_detail = async ( id ) => {
		this.fetchWP.get( 'memberships/get?id=' + id )
			.then( (json) => this.setState({ new_sub : {
				membership : id,
				object : json
			} }), (err) => this.notify( self.props.hubloy_membership.error, 'error' )
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
		var access = document.getElementsByClassName('hubloy_membership-membership-member-access');
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

	addSubscription( hubloy_membership, strings, id ) {
		const selected_membership = ( this.state.new_sub.membership > 0 && typeof this.state.new_sub.object !== "undefined" ) ? this.state.new_sub.object : false;
		const selected_access = this.state.selected_access;
		return(
			<div id="hubloy_membership-add-subscription" className="uk-flex-top" uk-modal="">
				<div className="uk-modal-dialog uk-margin-auto-vertical">
					<div className="uk-modal-header">
						<h2 className="uk-modal-title">{strings.edit.details.subscription.create.modal.title}</h2>
					</div>
					<button className="uk-modal-close-default" type="button" uk-close=""></button>
					<div className="uk-modal-body">
						<form className="uk-form-stacked" onSubmit={this.handleAssignMembership} ref={this.assign_membership}>
							<InputUI name={`member`} type={`hidden`} value={id}/>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.edit.details.subscription.create.modal.membership}</legend>
								<div className="uk-form-controls">
									<DropDownUI name={`membership`} values={this.state.memberships} class_name={`hubloy_membership-membership-list`} action={this.toggle_membership_fields}/>
								</div>
							</div>
							{selected_membership &&
								<React.Fragment>
									{selected_membership.trial_enabled &&
										<React.Fragment>
											<div className="uk-margin">
												<div className="uk-form-controls">
													<div className="hubloy_membership-input">
														<SwitchUI name={`enable_trial`} class_name={`enable_trial`} title={strings.edit.details.subscription.create.modal.enable_trial + selected_membership.trial_period + ' ' + selected_membership.trial_duration_text} value={`1`} action={this.toggle_trial_enabled} />
													</div>
												</div>
											</div>
										</React.Fragment>
									}
									<div className="hubloy_membership-membership-member-access">
										<div className="uk-margin">
											<legend className="uk-form-label">{strings.edit.details.subscription.create.modal.grant.title}</legend>
											<div className="uk-margin uk-grid-small uk-child-width-auto uk-grid">
												<label><input className="uk-radio" type="radio" name="access" value="date" onChange={this.member_access_type} onClick={this.member_access_type} checked={selected_access === 'date'} /> {strings.edit.details.subscription.create.modal.grant.date}</label>
												<label><input className="uk-radio" type="radio" name="access" value="permanent" onChange={this.member_access_type} onClick={this.member_access_type} checked={selected_access === 'permanent'}/> {strings.edit.details.subscription.create.modal.grant.permanent}</label>
												<label><input className="uk-radio" type="radio" name="access" value="invoice" onChange={this.member_access_type} onClick={this.member_access_type} checked={selected_access === 'invoice'}/> {strings.edit.details.subscription.create.modal.grant.invoice}</label>
											</div>
										</div>
										<div className="uk-margin" style={{display: ( selected_access === 'date' ? 'block' : 'none' )}}>
											<legend className="uk-form-label">{strings.labels.start_date}</legend>
											<div className="uk-form-controls">
												<InputUI name={`membership_start`} class_name={`hubloy_membership-from-date`} placeholder={strings.labels.start_date}/>
											</div>
										</div>
										<div className="uk-margin" style={{display: ( selected_access === 'date' ? 'block' : 'none' )}}>
											<legend className="uk-form-label">{strings.labels.end_date}</legend>
											<div className="uk-form-controls">
												<InputUI name={`membership_end`} class_name={`hubloy_membership-to-date`} placeholder={strings.labels.end_date}/>
											</div>
										</div>
									</div>
									<div className="uk-margin ">
										<button className="uk-button uk-button-primary uk-button-small save-button">{hubloy_membership.common.buttons.save}</button>
									</div>
								</React.Fragment>
							}
						</form>
					</div>
				</div>
			</div>
		)
	}

	subscriptionDetail() {
		var hubloy_membership = this.props.hubloy_membership,
			strings = hubloy_membership.strings,
			member = this.props.member;
		return (
			<div>
				<div className="uk-background-default uk-padding-small uk-margin-small-top hubloy_membership-margin-left-negative-40 uk-margin-remove-left">
					{this.state.plans.total > 0 &&
						<a className="uk-margin-small-left uk-button uk-button-primary uk-button-small" href="#hubloy_membership-add-subscription" uk-toggle="">{strings.edit.details.subscription.create.title}</a>
					}
					{this.state.plans.total > 0 ? (
							<PlanList plans={this.state.plans} hubloy_membership={hubloy_membership} action={this.refresh_member_plans}/>
						) : (
							<div className="uk-text-center">
								<a className="uk-button uk-button-primary uk-button-small" href="#hubloy_membership-add-subscription" uk-toggle="">{strings.edit.details.subscription.create.title}</a>
							</div>
						)
					}
				</div>
				{this.addSubscription( hubloy_membership, strings, member.id )}
			</div>
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
					<h3 className="uk-text-center uk-text-danger">{this.props.hubloy_membership.error}</h3>
				)
			} else {
				return this.subscriptionDetail();
			}
		}
	}
}

MemberSubscriptions.propTypes = {
	hubloy_membership: PropTypes.object
};