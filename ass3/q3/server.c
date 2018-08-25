/* credit @Daniel Scocco */

/****************** SERVER CODE ****************/

#include <stdio.h>
#include <netinet/in.h>
#include <string.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#define MAXSIZE 102400

int main(){
  int welcomeSocket, newSocket, num;
  char buffer[MAXSIZE];
  char filedata[MAXSIZE];
  struct sockaddr_in serverAddr;
  struct sockaddr_storage serverStorage;
  socklen_t addr_size;

  /*---- Create the socket. The three arguments are: ----*/
  /* 1) Internet domain 2) Stream socket 3) Default protocol (TCP in this case) */
  welcomeSocket = socket(PF_INET, SOCK_STREAM, 0);

  /*---- Configure settings of the server address struct ----*/
  /* Address family = Internet */
  serverAddr.sin_family = AF_INET;
  /* Set port number, using htons function to use proper byte order */
  serverAddr.sin_port = htons(8083);
  /* Set IP address to localhost */
  serverAddr.sin_addr.s_addr = inet_addr("127.0.0.1");
  /* Set all bits of the padding field to 0 */
  memset(serverAddr.sin_zero, '\0', sizeof serverAddr.sin_zero);

  /*---- Bind the address struct to the socket ----*/
  bind(welcomeSocket, (struct sockaddr *) &serverAddr, sizeof(serverAddr));

  /*---- Listen on the socket, with 5 max connection requests queued ----*/
  if(listen(welcomeSocket,5)==0){
    printf("I'm listening\n");
  } else {
    printf("Connection Error\n");
  }

  while(1){
    addr_size = sizeof serverStorage;
    newSocket = accept(welcomeSocket, (struct sockaddr *) &serverStorage, &addr_size);
    printf("Server got connection from a client\n");

    while(1){
      /*---- Accept call creates a new socket for the incoming connection ----*/
      num = recv(newSocket, buffer, MAXSIZE, 0);
      if(num==0){
        printf("Connection Closed\n");
        break;
      }
      printf("Server:Msg Received %s\n", buffer);
      char substrr[num];
      for(int i=0;i<num-1;i++)substrr[i] = buffer[i];
      substrr[num-1]='\0';
      printf("num %d substr %lu\n", num, strlen(substrr));

      FILE *fd = fopen(substrr, "rb");
      fread(filedata, 1, MAXSIZE, fd);
      fclose(fd);
      /*---- Send message to the socket of the incoming connection ----*/
      printf("%s\n",filedata);
      if ((send(newSocket, filedata, strlen(filedata), 0))== -1){
           fprintf(stderr, "Failure Sending Message\n");
           close(newSocket);
           break;
      }
      printf("Number of bytes sent: %lu\n", strlen(filedata));
    }
    close(newSocket);
  }
  close(welcomeSocket);
  return 0;
}
