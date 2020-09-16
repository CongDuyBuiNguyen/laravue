import request from '@/utils/request';

export function uploadFile(data) {
  return request({
    url: '/auth/uploadFile',
    method: 'post',
    data: data,
    headers: { 'content-type': 'multipart/form-data' },
  });
}

export function showListFile(query) {
  return request({
    url: '/auth/listFile',
    method: 'get',
    params: query,
  });
}

export function downloadFile(query) {
  return request({
    url: '/auth/downloadFile',
    method: 'get',
    params: query,
    responseType: 'arraybuffer',
  });
}

export function sendWorkerMessage(query) {
  return request({
    url: '/auth/sendWorkerMessage',
    method: 'get',
    params: query,
  });
}
