#include "stdio.h"

int main(int argc, char const *argv[]) {
    int rgrades[]= {40303,30322,40213,41203,41302,41212,40222,40312,31303,31312,31222,21322,30313,30223,31213,21313,21223,41311,20323,41122,40123,41113,31123};
    int rgrade[5];
    const float tnt = 4.2f;
    int lvl = 29;
    int addbp[] = {0,28,0,0,0};//没啥用。
    float initBP[5];
    float finalBP[5];
    float finalProp[5];

    for (size_t rg = 0; rg < 1; rg++) {
        rgrade[0] = rgrades[rg]%10;
        rgrade[1] = (rgrades[rg]/10)%10;
        rgrade[2] = (rgrades[rg]/100)%10;
        rgrade[3] = (rgrades[rg]/1000)%10;
        rgrade[4] = (rgrades[rg]/10000)%10;
        int dgrade[] = {25, 45, 18, 19, 5};


        for (int i = 0; i < 5; i++) {
            initBP[i] = 0.2f*(dgrade[i] + rgrade[i]);
            finalBP[i] = initBP[i];
        }
        for (int i = 2; i <= lvl; i++) {
            for (int j = 0; j < 5; j++) {
                finalBP[j] += ((tnt*dgrade[j]))/100;
            }
            finalBP[1] += 1;
            for (size_t i = 0; i < 5; i++) {
                float tmp = ((float)((int)(finalBP[i]*100.00)))/100;
                finalBP[i] = tmp;
                printf("%.4f/", tmp);
            }
            printf("\n");
        }
        finalProp[0] = 8*finalBP[0]+2*finalBP[1]+3*finalBP[2]+3*finalBP[3]+ 1*finalBP[4];
        finalProp[1] = 1*finalBP[0]+2*finalBP[1]+2*finalBP[2]+2*finalBP[3]+10*finalBP[4];
        finalProp[2] = 0.2*finalBP[0]+2.7*finalBP[1]+ 0.3*finalBP[2]+ 0.3*finalBP[3]+  0.2*finalBP[4];
        finalProp[3] =  0.2*finalBP[0]+ 0.3*finalBP[1]+3*finalBP[2]+ 0.3*finalBP[3]+  0.2*finalBP[4];
        finalProp[4] =  0.1*finalBP[0]+ 0.2*finalBP[1]+ 0.2*finalBP[2]+2*finalBP[3]+  0.1*finalBP[4];
        for (int i = 0; i < 5; i++) {
            finalProp[i] += 20;
        }
        for (int i = 0; i < 5; i++) {
            printf("%.0f/", finalProp[i]);
        }
        printf("\n");
    }

    return 0;
}
