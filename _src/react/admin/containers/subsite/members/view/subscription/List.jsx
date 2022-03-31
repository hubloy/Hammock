import React, { PureComponent } from 'react';

import fetchWP from 'utils/fetchWP';
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class PlanList extends PureComponent {

	constructor(props) {
		super(props);
		this.update_member_plan = React.createRef();
		this.state = {
			loading : true,
			error : false,
			statuses : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	async componentDidMount() {
		this.list_status();
	}

	notify(type, message) {
		toast[type](message, {toastId: 'members-sub-list-toast'});
	}

	list_status = async () => {
		this.fetchWP.get( 'members/list/status' )
			.then( (json) => this.setState({
				statuses : json,
				loading : false,
				error:false
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}

	toggle_membership_fields( target, selected ) {
		var id = jQuery(target).parent().attr('data-id'),
			item = jQuery('.hubloy_membership-plan-status-' + id);
		item.hide();
		if ( selected === 'active') {
			item.show();
		}
	}

	handleSubmit( event ) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.update_member_plan.current),
			$button = $form.find('button'),
			form = $form.serialize(),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'members/plan/update', form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success');
					if(typeof this.props.action !== 'undefined' && typeof this.props.action === 'function') {
						var action = this.props.action;
						action();
					}
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}

	delete_one(event) {
		event.preventDefault();
		var $button = jQuery(event.target),
			id = $button.attr('data-id'),
			$btn_txt = $button.text(),
			prompt = $button.attr('data-prompt'),
			self = this,
			hubloy_membership = self.props.hubloy_membership,
			helper = hubloy_membership.helper,
			error = hubloy_membership.error,
			action = false,
			fetchWP = this.fetchWP;

		
		if(typeof self.props.action !== 'undefined' && typeof self.props.action === 'function') {
			action = self.props.action;
		}
		helper.confirm( prompt, 'warning', function() {
			//continue
			$button.attr('disabled', 'disabled');
			$button.html("<div uk-spinner></div>");
			fetchWP.post( 'members/plan/remove', { plan : id } )
				.then( (json) => {
					if ( json.status ) {
						self.notify( json.message, 'success');
						if( action && typeof action === 'function') {
							action();
						}
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

	render() {
		const {plans, hubloy_membership} = this.props;
		const strings = hubloy_membership.strings;
		return(
			<React.Fragment>
				<h5 className="uk-margin-remove uk-heading-divider">{plans.total}{' '}{strings.edit.details.subscription.plans}</h5>
				<ul uk-accordion="">
					{plans.items.map(item =>
						<li key={item.id}>
							<a className="uk-padding-small uk-box-shadow-small uk-accordion-title" href="#">
								{item.membership ? item.membership.name : strings.labels.no_membership} - ( {item.plan_id} ) {item.has_trial ? '<span class="uk-badge">'+strings.labels.trial+'</span>' : ''}
							</a>
							<div className="uk-accordion-content">
								{item.membership ? (
									<React.Fragment>
										<p><span className="uk-text-bold">{strings.edit.details.subscription.list.plan_id}</span> : <code>{item.plan_id}</code></p>
										<p><span className="uk-text-bold">{strings.edit.details.subscription.list.date}</span> : {item.date_created}</p>
										<ul className="uk-list">
											<li>
												<span className="uk-text-bold">{strings.edit.details.subscription.list.type}</span> : {item.membership.type}
											</li>
											<li>
												<span className="uk-text-bold">{strings.edit.details.subscription.list.gateway}</span> : {item.gateway}
											</li>
										</ul>
										<h5 className="uk-heading-divider">{strings.edit.details.subscription.list.update}</h5>
										<form className="uk-form-horizontal" onSubmit={this.handleSubmit.bind(this)} ref={this.update_member_plan}>
											<InputUI name={`plan`} type={`hidden`} value={item.id}/>
											{!this.state.loading &&
												<div className="uk-margin">
													<legend className="uk-form-label">{strings.edit.details.subscription.list.status}</legend>
													<div className="uk-form-controls" data-id={item.id}>
														<DropDownUI name={`status`} values={this.state.statuses} value={item.status_simple} action={this.toggle_membership_fields.bind(this)}/>
													</div>
												</div>
											}
											<div className="uk-margin">
												<legend className="uk-form-label">{strings.edit.details.subscription.list.enabled}</legend>
												<div className="uk-form-controls">
													<div className="hubloy_membership-input">
														<SwitchUI name={`enabled`} checked={item.enabled} title={this.props.hubloy_membership.common.status.disabled} enabled_title={this.props.hubloy_membership.common.status.enabled} value={`1`} />
													</div>
												</div>
											</div>
											<div className={"uk-margin hubloy_membership-plan-status-" + item.id } style={{display: ( item.status_simple === 'active' ? 'block' : 'none' )}}>
												<legend className="uk-form-label">{strings.edit.details.subscription.list.sub_date}</legend>
												<div className="uk-form-controls">
													<InputUI name={`membership_start`} value={item.start_date_edit} class_name={`hubloy_membership-from-date`} placeholder={strings.labels.start_date}/>
												</div>
											</div>
											<div className={"uk-margin hubloy_membership-plan-status-" + item.id } style={{display: ( item.status_simple === 'active' ? 'block' : 'none' )}}>
												<legend className="uk-form-label">{strings.edit.details.subscription.list.expire_date}</legend>
												<div className="uk-form-controls">
													<InputUI name={`membership_end`} value={item.end_date_edit} class_name={`hubloy_membership-to-date`} placeholder={strings.labels.end_date}/>
												</div>
											</div>
											<div className="uk-margin ">
												<button className="uk-button uk-button-primary uk-button-small save-button">{hubloy_membership.common.buttons.update}</button>
											</div>
										</form>
										<a className="uk-link-text uk-text-danger" data-id={item.id} data-prompt={strings.edit.details.subscription.list.delete.one.prompt} onClick={this.delete_one.bind(this)} href="#">{strings.edit.details.subscription.list.delete.one.title}</a>
									</React.Fragment>
								) : (
									<span className="uk-text-bold">{strings.labels.no_membership}</span>
								)}
							</div>
						</li>
					)}
				</ul>
			</React.Fragment>
		)
	}
}