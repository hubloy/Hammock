import React, { Component } from 'react';
import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';
import EditRule from './Edit';
import { toast } from 'react-toastify';

export default class Table extends Component {

    constructor(props) {
		super(props);
		this.state = {
			pager: { current : 1, total : 0, pages : [] },
			items: [],
			enabled : false,
			loading : true,
			error : false,
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
		});

		this.getData = this.getData.bind(this);
		this.handleEdit = this.handleEdit.bind(this);
		this.handleDelete = this.handleDelete.bind(this);
	}

	notify(message,type) {
		toast[type](message, {toastId: 'rule-content-toast'});
	}

	getData = async ( page ) => {
		var type = this.props.type;
		this.fetchWP.get( 'rules/get/' + type + '?page=' + page )
			.then( (json) => {
				this.setState({
					items : json.items,
					pager : json.pager,
					enabled : json.enabled,
					loading : false,
					error : ! json.success,
				});
				window.hubloy_membership.helper.select2();
			}, (err) => this.setState({ loading : false, error : true })
		);
	}

	async componentDidMount() {
		this.getData( this.props.page );
	}

	async componentDidUpdate( prevProps ) {
		if ( this.props.type !== prevProps.type ) {
			this.setState({ loading : true, error : false });
			this.getData( this.props.page );
		}
	}

	handleRowAction(event, id, action ) {
		event.preventDefault();
		var $button = jQuery(event.target),
			hubloy_membership = this.props.hubloy_membership;
		if ( 'edit' == action ) {
			this.handleEdit( id );
		} else if ( 'delete' == action ) {
			this.handleDelete( $button, id, hubloy_membership );
		}
		return false;
	}

	handleEdit( id ) {
		UIkit.modal( jQuery( '#hubloy_membership-edit-rule-' + id ) ).show();
	}

	handleDelete( $button, id, hubloy_membership ) {
		var $btn_txt = $button.text(),
			prompt = $button.attr('data-prompt'),
			helper = hubloy_membership.helper,
			error = hubloy_membership.error,
			self = this,
			fetchWP = self.fetchWP;
		helper.confirm( prompt, 'warning', function() {
			$button.attr('disabled', 'disabled');
			$button.html("<div uk-spinner></div>");
			fetchWP.post( 'rules/delete', { rule : id } )
				.then( (json) => {
					if ( json.status ) {
						self.notify( json.message, 'success');
						self.setState({ loading : true, error : false });
						self.getData( self.props.page );
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
		const { pager, items, loading, error, enabled } = this.state;
        var hubloy_membership = this.props.hubloy_membership,
		    strings = hubloy_membership.strings;
		return (
			<React.Fragment>
				{ !loading && !error && !enabled && 
					<div className="uk-alert-warning" uk-alert="">
						<a className="uk-alert-close" uk-close=""></a>
						<p>{strings.dashboard.disabled}</p>
					</div>
				}
				{loading ? (
					<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
						<span className="uk-text-center" uk-spinner="ratio: 3"></span>
					</div>
				) : (
					error ? (
						<h3 className="uk-text-center uk-margin-top">{hubloy_membership.error}</h3>
					) : (
						pager.total <= 0 ? (
							<h3 className="uk-text-center uk-margin-top">{strings.dashboard.none}</h3>
						) : (
							<React.Fragment>
								<table className="uk-table uk-background-default">
									<thead>
										<tr>
											<th className='uk-table-shrink'><input className="uk-checkbox hubloy_membership-top-checkbox" type="checkbox" /></th>
											<th className="uk-table-shrink">{strings.dashboard.table.id}</th>
											<th>{strings.dashboard.table.desc}</th>
											<th>{strings.dashboard.table.status}</th>
											<th>{strings.dashboard.table.type}</th>
											<th>{strings.dashboard.table.date}</th>
										</tr>
									</thead>
									<tbody>
										{items.map(item =>
											<tr key={item.rule_id}>
												<td><input className="uk-checkbox" type="checkbox" value={item.rule_id} /></td>
												<td>{item.rule_id}</td>
												<td>
													<span dangerouslySetInnerHTML={{ __html: item.desc}}/>
													<div id={"rule-hover-"+ item.rule_id}>
														<a href="#" uk-tooltip={hubloy_membership.common.buttons.edit} title={hubloy_membership.common.buttons.edit} data-type={item.object_type} className="uk-text-primary" onClick={ e => this.handleRowAction(e, item.rule_id, 'edit')}>{hubloy_membership.common.buttons.edit}</a>
														{' '}|{' '}
														<a href="#" uk-tooltip={hubloy_membership.common.buttons.delete} title={hubloy_membership.common.buttons.delete} data-prompt={strings.dashboard.table.delete.prompt} className="uk-text-danger" onClick={ e => this.handleRowAction(e, item.rule_id, 'delete')}>{hubloy_membership.common.buttons.delete}</a>
													</div>
												</td>
												<td>{item.status_name}</td>
												<td><span dangerouslySetInnerHTML={{ __html: item.rule_name }}></span></td>
												<td>{item.date_created}</td>
											</tr>
										)}
									</tbody>
									<tfoot>
										<tr>
											<th className='uk-table-shrink'><input className="uk-checkbox hubloy_membership-top-checkbox" type="checkbox" /></th>
											<th className="uk-table-shrink">{strings.dashboard.table.id}</th>
											<th>{strings.dashboard.table.desc}</th>
											<th>{strings.dashboard.table.status}</th>
											<th>{strings.dashboard.table.type}</th>
											<th>{strings.dashboard.table.date}</th>
										</tr>
									</tfoot>
								</table>
								{items.map(item =>
									<EditRule key={item.rule_id} hubloy_membership={hubloy_membership} rule={item} rules={this.props.rules} />
								)}
							</React.Fragment>
						)
					)
				)}
				<PaginationUI pager={pager} onChange={this.getData} base={this.props.type}/>
			</React.Fragment>
		)
    }
}