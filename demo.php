<?php
public function index(){
		$data['title'] = 'General Settings';
		$result = $this->generalsettings_model->getData();
		if($this->input->post('btn_submit') == 1){
			$data_i = $this->input->post();
			unset($data_i['btn_submit']);
			unset($data_i['site_logo']);
			
			if(!empty($_FILES["site_logo"]['name'])){
				$image_info = getimagesize($_FILES["site_logo"]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				if($image_width > 255 || $image_height > 255){
					$this->session->set_flashdata('error','Site logo is greater then 255X255.');
					redirect('generalsettings/');
					exit();
				}
				//image upload
				$config['upload_path'] = 'assets/uploads/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size'] = '999999999';
				$config['max_width']  = '55555555';
				$config['max_height']  = '3000000';
				$config['file_name'] = time().'_'.$_FILES["site_logo"]['name'];
				
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('site_logo')){
					$this->session->set_flashdata('error',$this->upload->display_errors());
					redirect('generalsettings/');
					exit();
				}else{
					$data = $this->upload->data();
					$data_i['site_logo'] = $data['file_name'];
				}
			}
			
			$id = '';
			if($result){
				$id = $result[0]['id'];
			}
			
			$this->generalsettings_model->add_update($data_i, $id);
			if($id == ''){
				$this->session->set_flashdata('success','Settings Added Successfully.');
			}else{
				$this->session->set_flashdata('success','Settings Updated Successfully.');
			}
			redirect('generalsettings/');
		}
		if($result){
			$result = $result[0];
			$data['id'] = $result['id'];
			$data['site_name'] = $result['site_name'];
			$data['about_site'] = $result['about_site'];
			$data['site_email'] = $result['site_email'];
			$data['site_logo'] = $result['site_logo'];
			$data['site_fevicon'] = $result['site_fevicon'];
			$data['address'] = $result['address'];
			$data['contact'] = $result['contact'];
			$data['facebook'] = $result['facebook'];
		}
		$this->load->template('generalsettings',$data);
	}
	
	
	
	//model
	
	public function add_update($data, $id = ""){
		if($id == ''){
			$this->db->insert('tbl_general_settings',$data);
		}else{
			$this->db->where('id',$id);
			$this->db->update('tbl_general_settings',$data);
		}
		return;
	}
	
	public function getData($id = ''){
		$this->db->select('*');
		$this->db->from('tbl_general_settings');
		if($id != ''){
			$this->db->where('id',$id);
		}
		return $this->db->get()->result_array();
	}
